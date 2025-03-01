<?php

namespace App\Http\Resources;

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

        return [
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
            'defect_inspection_form' => $this->defectInspectionForm ? new DefectInspectionFormResource($this->defectInspectionForm) : null,
            'key_management' => $this->keyManagement ? new KeyManagementResource($this->keyManagement) : null,
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
            'status' => $this->status,
            'pre_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[0] ?? null),
            'p1_completion' => $this->calculatePhaseCompletion($this->progressPhases[1] ?? null),
            'p2a_completion' => $this->calculatePhaseCompletion($this->progressPhases[2] ?? null),
            'p2b_completion' => $this->calculatePhaseCompletion($this->progressPhases[3] ?? null),
            'iot_completion' => $this->calculatePhaseCompletion($this->progressPhases[4] ?? null),
            'post_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[5] ?? null),
            'resource_id' => $this->resource_id,
            'resource_item_id' => $this->resourceItem->id,
            'permission_id' => $this->permission_id,
            'permissions' => $this->itemPermissions->userPermissions,
            // 'completed_at' => $this->completed_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
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
