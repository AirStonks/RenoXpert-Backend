<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignPackageResource extends JsonResource
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
            'campaign_id' => $this->campaign_id,
            'campaign' => $this->whenLoaded('campaign', function () {
                return new CampaignResource($this->campaign);
            }),
            'name' => $this->name,
            'description' => $this->description,
            'internal_description' => $this->internal_description,
            'bookings' => $this->whenLoaded('bookings', function () {
                return BookingResource::collection($this->bookings);
            }),
            'base_amount' => $this->base_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'slot_total' => $this->slot_total,
            'slot_used' => $this->slot_used,
            'slot_remaining' => $this->slot_remaining,
            'status' => $this->status,
            'metadata' => $this->metadata,
        ];
    }
}
