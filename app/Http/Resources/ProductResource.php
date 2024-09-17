<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'SKU' => $this->SKU,
            'category' => $this->category ? $this->category->name : null,
            'type' => $this->type,
            'remark' => $this->remark,
            'price' => $this->price,
            'premium_price' => $this->premium_price,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'status' => $this->status,
        ];
    }
}
