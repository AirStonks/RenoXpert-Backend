<?php

namespace App\Http\Resources;

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
            'defect_inspection_form' => $this->defectInspectionForm ? new DefectInspectionFormResource($this->defectInspectionForm) : null,
            'phases' => ProgressPhaseResource::collection($this->progressPhases),
            'status' => $this->status,
            // 'completed_at' => $this->completed_at->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
