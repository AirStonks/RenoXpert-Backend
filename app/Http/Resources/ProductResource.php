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
        // return $request->all();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'SKU' => $this->SKU,
            'category_id' => $this->category_id,
            'category' => $this->category ? $this->category->name : null,
            'type' => $this->type,
            'description' => $this->description,
            'uom' => $this->uom,
            'provisioning' => [
                'supply' => $this->productSupply,
                'install' => $this->productInstall,
            ],
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'status' => $this->status,
        ];
    }
}
