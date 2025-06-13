<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class RenoProgressResourceAdTable extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $orderWithOnlyUser = $this->sale->order->user;

        // Declare a blank Order Modal
        $order = new Order();
        $order->user = $orderWithOnlyUser;

        $sale = new Sale();
        $sale->order = $order;

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
            'sale' => $sale,
            'sale_id' => $this->sale->id,
            'sales_no' => $this->sale->sales_no,
            'status' => $this->status,
            'remaining_percentage' => $this->sale->remaining_percentage,
            'paid_percentage' => $this->sale->invoices->where('status', 'paid')->sum('percentage'),
            'rpm_version' => $this->rpm_version,
            'sent_to_lark_date' => $this->sent_to_lark_date ? Carbon::parse($this->sent_to_lark_date)->format('d/m/Y') : null,
            // 'completed_at' => $this->completed_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];

        // Include completion fields only if rpm_version is 1 or 2
        if (in_array($this->rpm_version, [1, 2])) {
            $data = array_merge($data, [
                'progress' => $this->calculateProgressDetails(),
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
                'date_management' => $this->date_management,
            ]);
        }

        return $data;
    }

    /**
     * Calculate detailed progress for individual jobs within phases.
     *
     * @return array<string, float>
     */
    private function calculateProgressDetails(): array
    {
        $progress = [];

        // Assuming progressPhases[0] is Pre-Reno
        if (isset($this->progressPhases[0]['jobs'])) {
            $preRenoJobs = $this->progressPhases[0]['jobs'];
            $progress['pre_reno_1'] = $this->calculateJobCompletion($preRenoJobs[0] ?? null);

            $progress['pre_reno_2']['pre_reno_2_1'] = $this->calculateTaskCompletion($preRenoJobs[1]['tasks'][0] ?? null);
            $progress['pre_reno_2']['pre_reno_2_2'] = $this->calculateTaskCompletion($preRenoJobs[1]['tasks'][1] ?? null);
            $progress['pre_reno_2']['pre_reno_2_3'] = $this->calculateTaskCompletion($preRenoJobs[1]['tasks'][2] ?? null);

            $progress['pre_reno_3']['pre_reno_3_1'] = $this->calculateTaskCompletion($preRenoJobs[2]['tasks'][0] ?? null);
            $progress['pre_reno_3']['pre_reno_3_2'] = $this->calculateTaskCompletion($preRenoJobs[2]['tasks'][1] ?? null);
            $progress['pre_reno_3']['pre_reno_3_3'] = $this->calculateTaskCompletion($preRenoJobs[2]['tasks'][2] ?? null);
            $progress['pre_reno_3']['pre_reno_3_4'] = $this->calculateTaskCompletion($preRenoJobs[2]['tasks'][3] ?? null);
        }

        // Assuming progressPhases[1] is P1
        if (isset($this->progressPhases[1]['jobs'])) {
            $p1Jobs = $this->progressPhases[1]['jobs'];
            $progress['p1_1'] = $this->calculateJobCompletion($p1Jobs[1] ?? null);
            $progress['p1_2'] = $this->calculateJobCompletion($p1Jobs[0] ?? null);
            $progress['p1_3'] = $this->calculateJobCompletion($p1Jobs[2] ?? null);
        }

        // Add more phases (e.g., P2a, P2b) as needed...

        return $progress;
    }

    /**
     * Calculate completion percentage for a single job.
     *
     * @param mixed $job
     * @return float
     */
    private function calculateJobCompletion($job): float
    {
        if (!$job || !isset($job['tasks']) || empty($job['tasks'])) {
            return 0.0;
        }

        $totalWeightage = 0;
        $weightedCompletion = 0;

        foreach ($job['tasks'] as $task) {
            $weightage = $task['task_weightage'];
            $statusWeightage = match ($task['status']) {
                'not_available', 'submitted', 'completed' => 1.0,
                'in_progress' => 0.75,
                'started' => 0.25,
                'not_started' => 0.0,
                default => 0.0,
            };

            $totalWeightage += $weightage;
            $weightedCompletion += $weightage * $statusWeightage;
        }

        return $totalWeightage > 0 ? $weightedCompletion / $totalWeightage : 0.0;
    }

    /**
     * Calculate completion percentage for a single task.
     *
     * @param mixed $task
     * @return float
     */
    private function calculateTaskCompletion($task): float
    {
        return match ($task['status']) {
            'not_available', 'submitted', 'completed' => 1.0,
            'in_progress' => 0.75,
            'started' => 0.25,
            'not_started' => 0.0,
            default => 0.0,
        };
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
}
