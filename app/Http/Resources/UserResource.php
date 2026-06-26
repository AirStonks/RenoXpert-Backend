<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'name_first' => $this->name_first,
            'name_last' => $this->name_last,
            'name_preferred' => $this->name_preferred,
            'salutations' => $this->salutations,
            'ic' => $this->ic,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'phone_no' => $this->phone_no,
            'type' => $this->type,
            'referral_code' => $this->referral_code,
            'address' => $this->address ? $this->address : null,
            'status' => $this->status,
            'onboarded_at' => $this->onboarded_at,
            'agent_approved_at' => $this->agent_approved_at,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
