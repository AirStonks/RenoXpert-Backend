<?php

namespace App\Http\Resources\API\OwnerCS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'phone_no' => $this->phone_no,
        ];
    }
}
