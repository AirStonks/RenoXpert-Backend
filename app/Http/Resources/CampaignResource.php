<?php

namespace App\Http\Resources;

use App\Models\User;
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
            'packages' => $this->whenLoaded('packages', function () {
                return CampaignPackageResource::collection($this->packages);
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
