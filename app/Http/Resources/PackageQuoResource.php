<?php

namespace App\Http\Resources;

use App\Models\Catalog\Product;
use App\Models\Foundation\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageQuoResource extends JsonResource
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
            'quotation_package_id' => $this->id,
            'id' => $this->package->id,
            'name' => $this->package->name,
            'category' => $this->package->category,
            'description' => $this->package->description,
            'description_internal' => $this->package->description_internal,
            'total_price' => $totalPrice,
            'quantity' => $this->pivot->quantity ?? 1,
            'products' => ProductQuoResource::collection($this->quoProducts),
            // 'products' => $this->quoProducts,
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
