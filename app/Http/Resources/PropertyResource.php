<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $property = [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'street' => $this->street,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'state' => $this->state,
            'description' => $this->description,
            'thumbnail_url' => $this->thumbnail_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // If request has propertyRoi, include it
        if ($this->propertyRoi) {
            $property['propertyRoi'] = new PropertyROIResource($this->propertyRoi);
        }

        return $property;
    }
}
