<?php

namespace App\Http\Resources\Campaign;

use App\Http\Resources\CampaignPackageResource;
use App\Http\Resources\OrderResource;
use App\Models\Sale;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'packages' => $this->whenLoaded('packages', function () {
                return CampaignPackageResource::collection($this->packages);
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
        ];
    }
}
