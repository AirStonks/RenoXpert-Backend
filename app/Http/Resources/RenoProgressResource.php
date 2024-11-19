<?php

namespace App\Http\Resources;

use App\Models\Sale;
use Illuminate\Http\Request;
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
        return [
            'id' => $this->id,
            'sale_id' => $this->sale->id,
            'sale' => new SaleResource($this->sale),
            'defect_inspection_form' => $this->defectInspectionForm ? new DefectInspectionFormResource($this->defectInspectionForm) : null,
            'phases' => ProgressPhaseResource::collection($this->progressPhases),
            // Ensure that start_date and end_date are DateTime objects before calling format()
            'start_date' => $this->start_date ? \Carbon\Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'end_date' => $this->end_date ? \Carbon\Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'status' => $this->status,
            'pre_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[0] ?? null),
            'reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[1] ?? null),
            'post_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[2] ?? null),
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

        $totalWeightage = 0;
        $weightedCompletion = 0;

        foreach ($phase['jobs'] as $job) {
            foreach ($job['tasks'] as $task) {
                $weightage = $task['task_weightage'];
                $statusWeightage = match ($task['status']) {
                    'completed' => 1.0,
                    'in_progress' => 0.5,
                    'not_started' => 0.0,
                    default => 0.0,
                };

                $totalWeightage += $weightage;
                $weightedCompletion += $weightage * $statusWeightage;
            }
        }

        return $totalWeightage > 0 ? round($weightedCompletion / $totalWeightage, 2) : 0.0;
    }
}
