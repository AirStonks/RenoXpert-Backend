<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Http\Resources\CampaignLayoutTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'internal_description' => $this->internal_description,
            'thumbnail' => $this->thumbnail,
            'thumbnail_video' => $this->thumbnail_video,
            'packages' => $this->whenLoaded('packages', function () {
                return CampaignPackageResource::collection($this->packages);
            }),
            'layout_types' => $this->whenLoaded('layoutTypes', function () {
                return CampaignLayoutTypeResource::collection($this->layoutTypes);
            }),
            'bookings' => $this->whenLoaded('bookings', function () {
                return BookingResource::collection($this->bookings);
            }),
            'base_amount' => $this->base_amount,
            'booking_amount' => $this->booking_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'published_at' => $this->published_at,
            'published_by' => $this->published_by,
            'slot_total' => $this->slot_total,
            'slot_used' => $this->slot_used,
            'slot_remaining' => $this->slot_remaining,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
