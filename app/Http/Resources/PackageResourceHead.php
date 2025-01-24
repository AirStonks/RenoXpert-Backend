<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResourceHead extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalPrice = $this->calculateTotalPrice();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'description_internal' => $this->description_internal,
            'total_price' => $totalPrice,
            // 'products' => ProductResource::collection($this->products),
            'status' => $this->status,
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }

    private function calculateTotalPrice(): float
    {
        if (!$this->products || $this->products->isEmpty()) {
            return 0;
        }

        return $this->products->sum(function ($product) {
            $originProduct = Product::find($product->id);

            if ($originProduct->id !== $product->id) {
                return 0;
            }

            $quantity = $product->pivot->quantity;
            $retailPriceProvisioning = $originProduct->productInstall->retail_price;
            $retailPriceSupply = $originProduct->productSupply->retail_price;

            return ($quantity * $retailPriceProvisioning) + ($quantity * $retailPriceSupply);
        });
    }
}
