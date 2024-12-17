<?php

namespace App\Http\Resources;

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
            'user' => $this->sale->user,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'status' => $this->status,
            'pre_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[0] ?? null),
            'reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[1] ?? null),
            'post_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[2] ?? null),
            'completed_at' => $this->completed_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];

        // Include phases if flag is set to true
        if ($this->includePhases) {
            $data['phases'] = $this->filterPhasesWithVisibleTasks();
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

    private function calculateJobProgress($job) {
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
                    $filteredTasks = collect($job->tasks)->where('is_visible', true)->values();

                    // Reassign filtered tasks to the job
                    $job->tasks = $filteredTasks->values();

                    
                    return $job;
                });

                return $phase;
            })
        );
    }
}
