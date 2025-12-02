<?php

namespace App\Http\Resources\Operation;

use App\Models\Sale;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class RenoProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $orderWithOnlyUser = $this->mainSale->order->user;

        // Declare a blank Order Modal
        $order = new Order();
        $order->user = $orderWithOnlyUser;

        $sale = new Sale();
        $sale->order = $order;

        $data = [
            'id' => $this->id,
            'sale_id' => $this->mainSale->id,
            'property' => [
                'id' => $this->mainSale->order->property->id,
                'name' => $this->mainSale->order->property->name,
                'block' => $this->mainSale->order->block,
                'floor' => $this->mainSale->order->floor,
                'unit_no' => $this->mainSale->order->unit_no,
            ],
            'sale' => $sale,
            'sale_id' => $this->mainSale->id,
            'sales_no' => $this->mainSale->sales_no,
            // Ensure that start_date and end_date are DateTime objects before calling format()
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'contractual_start_date' => $this->contractual_start_date ? Carbon::parse($this->contractual_start_date)->format('Y-m-d') : null,
            'contractual_end_date' => $this->contractual_end_date ? Carbon::parse($this->contractual_end_date)->format('Y-m-d') : null,
            'contractual_p1_start_date' => $this->contractual_p1_start_date ? Carbon::parse($this->contractual_p1_start_date)->format('Y-m-d') : null,
            'contractual_p1_end_date' => $this->contractual_p1_end_date ? Carbon::parse($this->contractual_p1_end_date)->format('Y-m-d') : null,
            'contractual_p2_start_date' => $this->contractual_p2_start_date ? Carbon::parse($this->contractual_p2_start_date)->format('Y-m-d') : null,
            'contractual_p2_end_date' => $this->contractual_p2_end_date ? Carbon::parse($this->contractual_p2_end_date)->format('Y-m-d') : null,
            'contractual_qc_start_date' => $this->contractual_qc_start_date ? Carbon::parse($this->contractual_qc_start_date)->format('Y-m-d') : null,
            'contractual_qc_end_date' => $this->contractual_qc_end_date ? Carbon::parse($this->contractual_qc_end_date)->format('Y-m-d') : null,
            'contractual_pc_start_date' => $this->contractual_pc_start_date ? Carbon::parse($this->contractual_pc_start_date)->format('Y-m-d') : null,
            'contractual_pc_end_date' => $this->contractual_pc_end_date ? Carbon::parse($this->contractual_pc_end_date)->format('Y-m-d') : null,
            'contractual_handover_date' => $this->contractual_handover_date ? Carbon::parse($this->contractual_handover_date)->format('Y-m-d') : null,
            'contractor_start_date' => $this->contractor_start_date ? Carbon::parse($this->contractor_start_date)->format('Y-m-d') : null,
            'contractor_end_date' => $this->contractor_end_date ? Carbon::parse($this->contractor_end_date)->format('Y-m-d') : null,
            'contractor_p1_start_date' => $this->contractor_p1_start_date ? Carbon::parse($this->contractor_p1_start_date)->format('Y-m-d') : null,
            'contractor_p1_end_date' => $this->contractor_p1_end_date ? Carbon::parse($this->contractor_p1_end_date)->format('Y-m-d') : null,
            'contractor_p2_start_date' => $this->contractor_p2_start_date ? Carbon::parse($this->contractor_p2_start_date)->format('Y-m-d') : null,
            'contractor_p2_end_date' => $this->contractor_p2_end_date ? Carbon::parse($this->contractor_p2_end_date)->format('Y-m-d') : null,
            'contractor_qc_start_date' => $this->contractor_qc_start_date ? Carbon::parse($this->contractor_qc_start_date)->format('Y-m-d') : null,
            'contractor_qc_end_date' => $this->contractor_qc_end_date ? Carbon::parse($this->contractor_qc_end_date)->format('Y-m-d') : null,
            'contractor_pc_start_date' => $this->contractor_pc_start_date ? Carbon::parse($this->contractor_pc_start_date)->format('Y-m-d') : null,
            'contractor_pc_end_date' => $this->contractor_pc_end_date ? Carbon::parse($this->contractor_pc_end_date)->format('Y-m-d') : null,
            'contractor_handover_date' => $this->contractor_handover_date ? Carbon::parse($this->contractor_handover_date)->format('Y-m-d') : null,
            'status' => $this->status,
            'remaining_percentage' => $this->mainSale->remaining_percentage,
            'paid_percentage' => $this->mainSale->invoices->where('status', 'paid')->sum('percentage'),
            'rpm_version' => $this->rpm_version,
            'owner_handover_released_at' => $this->owner_handover_released_at,
            'owner_handover_submitted_at' => $this->owner_handover_submitted_at,
            // 'completed_at' => $this->completed_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];

        // Include completion fields only if rpm_version is 1 or 2
        if (in_array($this->rpm_version, [1, 2])) {
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
                    'not_available' => 1.0,
                    'submitted' => 1.0,
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
