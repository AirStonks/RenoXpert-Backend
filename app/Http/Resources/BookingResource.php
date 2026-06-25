<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'campaign_package_id' => $this->campaign_package_id,
            'user_id' => $this->user_id,
            'referred_by_user_id' => $this->referred_by_user_id,
            'referral_code' => $this->referral_code,
            'referred_by' => $this->whenLoaded('referredBy', function () {
                return [
                    'id' => $this->referredBy->id,
                    'name' => $this->referredBy->name,
                    'referral_code' => $this->referredBy->referral_code,
                ];
            }),
            'booking_no' => $this->booking_no,
            'booking_hash' => $this->booking_hash,
            'amount' => $this->amount,
            'payment_url' => $this->payment_url,
            'booked_at' => $this->booked_at,
            'expired_at' => $this->expired_at,
            'internal_remark' => $this->internal_remark,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
