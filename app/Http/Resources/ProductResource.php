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
            'product_retail_price' => $this->product_retail_price,
            'product_cost_of_good_sold' => $this->product_cost_of_good_sold,
            'product_excluded_price' => $this->product_excluded_price,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'status' => $this->status,
        ];
    }
}
