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
            'name' => $this->name_first . ' ' . $this->name_last,
            'name_first' => $this->name_first,
            'name_last' => $this->name_last,
            'name_preferred' => $this->name_preferred,
            'salutations' => $this->salutations,
            'ic' => $this->ic,
            'email' => $this->email,
            'phone_no' => $this->phone_no,
            'type' => $this->type,
            'address' => $this->address ? $this->address : null,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
