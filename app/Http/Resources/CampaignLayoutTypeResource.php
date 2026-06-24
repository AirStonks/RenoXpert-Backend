<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignLayoutTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sort' => $this->sort,
            'rental_projection' => $this->rental_projection,
            'layout_thumbnail' => $this->layout_thumbnail,
            'rendering_images' => $this->rendering_images,
        ];
    }
}
