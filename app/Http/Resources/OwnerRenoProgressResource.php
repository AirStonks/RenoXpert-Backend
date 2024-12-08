<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class OwnerRenoProgressResource extends JsonResource
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
            'phases' => ProgressPhaseResource::collection($this->progressPhases),
            // Ensure that start_date and end_date are DateTime objects before calling format()
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'status' => $this->status,
            'pre_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[0] ?? null),
            'reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[1] ?? null),
            'post_reno_completion' => $this->calculatePhaseCompletion($this->progressPhases[2] ?? null),
            // 'completed_at' => $this->completed_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
