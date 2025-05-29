<?php

namespace App\Http\Resources;

use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class OwnerRenoProgressResource extends JsonResource
{
    protected $includePhases;
    public function __construct($resource, $includePhases = false)
    {
        parent::__construct($resource);
        $this->includePhases = $includePhases;  // Store the user or other parameter
    }

    /**
     * Create a new resource collection.
     *
     * @param mixed $resource
     * @param bool $includePhases
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource, $includePhases = false)
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function ($collection) use ($includePhases) {
            $collection->each(function ($resource) use ($includePhases) {
                $resource->includePhases = $includePhases;
            });
        });
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $oldRenoProgress = RenoProgress::where('sale_id', $this->sale_id)->onlyTrashed()->first();

        $data = [
            'id' => $this->id,
            'sale_id' => $this->sale->id,
            'property' => [
                'id' => $this->sale->order->property->id,
                'name' => $this->sale->order->property->name,
                'block' => $this->sale->order->block,
                'floor' => $this->sale->order->floor,
                'unit_no' => $this->sale->order->unit_no,
            ],
            'sale' => new SaleResource($this->sale),
            'user' => $this->sale->user,
            'contractual_start_date' => $this->contractual_start_date ? Carbon::parse($this->contractual_start_date)->format('Y-m-d') : null,
            'contractual_end_date' => $this->contractual_end_date ? Carbon::parse($this->contractual_end_date)->format('Y-m-d') : null,
            'contractor_start_date' => $this->contractor_start_date ? Carbon::parse($this->contractor_start_date)->format('Y-m-d') : null,
            'contractor_end_date' => $this->contractor_end_date ? Carbon::parse($this->contractor_end_date)->format('Y-m-d') : null,
            'status' => $this->status,
            'rpm_jobs' => $this->rpm_version === 3 ? RPMJobResource::collection($this->rpmJobs) : null,
            'date_management' => $this->date_management,
            'completed_at' => $this->completed_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];

        // Include completion fields only if rpm_version is 1 or 2
        if (in_array($this->rpm_version, [1, 2])) {

            // Include phases if flag is set to true
            if ($this->includePhases) {
                $data['phases'] = $this->filterPhasesWithVisibleTasks();
            }

            $data = array_merge($data, [
                'pre_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[0] ?? null),
                'p1_completion' => $this->calculatePhaseCompletion($this->progressPhases[1] ?? null),
                'p2a_completion' => $this->calculatePhaseCompletion($this->progressPhases[2] ?? null),
                'p2b_completion' => $this->calculatePhaseCompletion($this->progressPhases[3] ?? null),
                'iot_completion' => $this->calculatePhaseCompletion($this->progressPhases[4] ?? null),
                'post_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[5] ?? null),
            ]);
        }

        if ($this->rpm_version === 3) {
            $data = array_merge($data, [
                'completion' => $this->calculateV3Completion(),
            ]);
        }

        if (!is_null($oldRenoProgress)) {
            $data = array_merge($data, [
                'is_converted' => true,
            ]);
        }

        return $data;
    }

    /**
     * Calculate the completion percentage for a given phase.
     *
     * @param mixed $phase
     * @return float
     */
    private function calculatePhaseCompletion($phase): float
    {
        if (!$phase || !isset($phase['jobs'])) {
            return 0.0;
        }
        $totalJobs = count($phase['jobs']);
        if ($totalJobs === 0) {
            return 0.0;
        }
        $totalJobCompletionPercentage = 0;
        foreach ($phase['jobs'] as $job) {
            $jobCompletionPercentage = 0;
            $totalWeightage = 0;
            $weightedCompletion = 0;
            foreach ($job['tasks'] as $task) {
                $weightage = $task['task_weightage'];
                $statusWeightage = match ($task['status']) {
                    'completed' => 1.0,
                    'in_progress' => 0.75,
                    'started' => 0.25,
                    'not_started' => 0.0,
                    default => 0.0,
                };
                $totalWeightage += $weightage;
                $weightedCompletion += $weightage * $statusWeightage;
            }
            // Calculate job completion percentage
            if ($totalWeightage > 0) {
                $jobCompletionPercentage = $weightedCompletion / $totalWeightage;
            }
            $totalJobCompletionPercentage += $jobCompletionPercentage;
        }
        // Calculate the phase completion percentage (average of job completion percentages)
        return $totalJobCompletionPercentage / $totalJobs;
    }

    private function calculateJobProgress($job)
    {
        // Define the status weightages
        $statusWeights = [
            'not_started' => 0,
            'started' => 0.25,
            'in_progress' => 0.75,
            'completed' => 1,
        ];

        // Initialize the weighted sum and total weight
        $weightedSum = 0;
        $totalWeight = 0;

        // Loop through the tasks to calculate the weighted sum and total weight
        foreach ($job->tasks as $task) {
            $statusWeight = isset($statusWeights[$task->status]) ? $statusWeights[$task->status] : 0;
            $taskWeight = isset($task->task_weightage) ? $task->task_weightage : 1; // Default to 1 if not provided

            // Add to the weighted sum
            $weightedSum += $taskWeight * $statusWeight;

            // Add to the total weight
            $totalWeight += $taskWeight;
        }

        // Return the progress percentage (multiply by 100 to get percentage)
        return $totalWeight > 0 ? ($weightedSum / $totalWeight) * 100 : 0;
    }

    /**
     * Filter tasks where is_visible === true and return the phases.
     *
     * @return AnonymousResourceCollection
     */
    private function filterPhasesWithVisibleTasks()
    {
        return ProgressPhaseResource::collection(
            $this->progressPhases->map(function ($phase) {
                // Filter jobs and their tasks

                $phase->jobs = $phase->jobs->map(function ($job) {

                    $job->completion = $this->calculateJobProgress($job);
                    $filteredTasks = collect($job->tasks)->where('is_visible', true)->map(function ($task) {
                        // Remove 'internal_comment' from each task
                        unset($task->internal_comment);
                        return $task;
                    });

                    // Reassign filtered tasks to the job
                    $job->tasks = $filteredTasks->values();


                    return $job;
                });

                return $phase;
            })
        );
    }

    private function calculateV3Completion(): array
    {
        // Define status weightage mapping
        $statusWeightage = [
            'not-available' => 1.0,
            'not-started' => 0.0,
            'pending' => 0.25,
            'in-progress' => 0.5,
            'completed' => 1.0,
        ];

        // Fetch all jobs with their tasks
        $jobs = $this->rpmJobs;
        $jobCompletions = [];
        $totalJobs = $jobs->count();
        $sumJobCompletions = 0;

        foreach ($jobs as $job) {
            $tasks = $job->rpmTasks;
            $totalTasks = $tasks->count();
            $sumTaskCompletion = 0;

            // Calculate completion for each task
            foreach ($tasks as $task) {
                $status = $task->status;
                $taskCompletion = $statusWeightage[$status] ?? 0.0; // Default to 0 if status is invalid
                $sumTaskCompletion += $taskCompletion;
            }

            // Calculate job completion (avoid division by zero)
            $jobCompletion = $totalTasks > 0 ? ($sumTaskCompletion / $totalTasks) * 100 : 0.0;
            $jobCompletions[] = [
                'job_id' => $job->id,
                'job_name' => $job->name,
                'completion_percentage' => $jobCompletion / 100,
            ];

            $sumJobCompletions += $jobCompletion;
        }

        // Calculate overall completion (avoid division by zero)
        $overallCompletion = $totalJobs > 0 ? ($sumJobCompletions / $totalJobs) : 0.0;

        return [
            'jobs' => $jobCompletions,
            'overall_completion' => $overallCompletion / 100,
        ];
    }
}
