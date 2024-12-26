<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Initialize total_price to 0
        $totalPrice = 0;

        // Early return if there are no products
        if (!$this->products || $this->products->isEmpty()) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'category' => $this->category,
                'description' => $this->description,
                'description_internal' => $this->description_internal,
                'total_price' => $totalPrice,
                'products' => ProductResource::collection($this->products),
                'created_at' => $this->created_at->format('d/m/Y'),
                'updated_at' => $this->updated_at->format('d/m/Y'),
            ];
        }

        // Calculate total price if there are products
        if ($this->products) {
            foreach ($this->products as $product) {

                $originProduct = Product::find($product->id);

                if ($originProduct->id === $product->id) {
                    // Assuming the formatted product retains access to original pivot and provisioning data
                    $quantity = $product->pivot->quantity; // Use $product->resource to access the original model
                    $retailPriceProvisioning = $originProduct->productInstall->retail_price;
                    $retailPriceSupply = $originProduct->productSupply->retail_price;

                    // Calculate total price for each product
                    $totalPrice += ($quantity * $retailPriceProvisioning) + ($quantity * $retailPriceSupply);
                }
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'description_internal' => $this->description_internal,
            'total_price' => $totalPrice,
            'products' => ProductResource::collection($this->products),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
