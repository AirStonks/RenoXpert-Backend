<?php

namespace App\Http\Resources\List;

use App\Http\Resources\CampaignPackageResource;
use App\Http\Resources\OrderResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Calculate slot values based on packages if they exist
        $slotTotal = $this->slot_total;
        $slotUsed = $this->slot_used;
        $slotRemaining = $this->slot_remaining;

        // If packages are loaded and exist, recalculate slot values from packages
        if ($this->packages->isNotEmpty()) {
            $slotTotal = $this->packages->sum('slot_total');
            $slotUsed = $this->packages->sum('slot_used');
            $slotRemaining = $this->packages->sum('slot_remaining');
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'internal_description' => $this->internal_description,
            'base_amount' => $this->base_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'published_at' => $this->published_at,
            'published_by' => $this->published_by,
            'slot_total' => $slotTotal,
            'slot_used' => $slotUsed,
            'slot_remaining' => $slotRemaining,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'order_id' => $this->order_id,
            'order' => $this->when($this->order_id && $this->relationLoaded('order'), function () {
                return new OrderResource($this->order);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
