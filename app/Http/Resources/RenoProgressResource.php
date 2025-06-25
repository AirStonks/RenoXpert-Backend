<?php

namespace App\Http\Resources;

use App\Models\RenoProgress;
use App\Models\ResourceItem;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class RenoProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $resourceItem = ResourceItem::where('resource_id', $this->resource_id)
        //     ->where('item_reference_id', $this->id)
        //     ->first();

        $oldRenoProgress = RenoProgress::where('sale_id', $this->sale_id)->onlyTrashed()->first();

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
            'sale' => new SaleResource($this->mainSale),
            'defect_inspection_form' => $this->defectInspectionForm ? new DefectInspectionFormResource($this->defectInspectionForm) : null,
            'key_management' => $this->keyManagement ? new KeyManagementResource($this->keyManagement) : null,
            'date_management' => $this->date_management,
            'status' => $this->status,
            'rpm_jobs' => $this->rpm_version === 3 ? RPMJobResource::collection($this->rpmJobs) : null,
            'total_amount' => $this->sales->sum('total_amount'),
            'paid_amount' => $this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * $invoice->percentage;
                });
            })->sum(),
            'remaining_percentage' => (1 - $this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * ($invoice->percentage / 100);
                });
            })->sum() / $this->sales->sum('total_amount')) * 100,
            'paid_percentage' => ($this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * ($invoice->percentage / 100);
                });
            })->sum() / $this->sales->sum('total_amount')) * 100,
            'resource_id' => $this->resource_id,
            'resource_item_id' => $this->resourceItem->id,
            'permission_id' => $this->permission_id,
            'permissions' => $this->itemPermissions->userPermissions,
            'rpm_version' => $this->rpm_version,
            'sent_to_lark_date' => $this->sent_to_lark_date ? Carbon::parse($this->sent_to_lark_date)->format('d/m/Y') : null,
            "rpm_acknowledge_status" => $this->rpm_acknowledge_status,
            'completed_at' => $this->completed_at?->format('d/m/Y'),
            'defect_updated_at' => $this->defect_updated_at?->format('d/m/Y'),
            'permit_updated_at' => $this->permit_updated_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];

        // Include completion fields only if rpm_version is 1 or 2
        if (in_array($this->rpm_version, [1, 2])) {
            $data = array_merge($data, [
                'phases' => ProgressPhaseResource::collection($this->progressPhases),
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
