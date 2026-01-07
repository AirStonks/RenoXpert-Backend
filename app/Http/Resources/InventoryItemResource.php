<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'product_id' => $this->product_id,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'zone' => $this->zone,
            'created_at' => $this->created_at ? $this->created_at->format('d/m/Y') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y') : null,
        ];

        // Calculate aggregated stock values from variants if variants are loaded
        if ($this->relationLoaded('variants')) {
            $variants = $this->variants;
            
            // Calculate totals from variants
            $totalStock = $variants->sum('in_stock');
            $totalProjectedStock = $variants->sum('projected_stock');
            $totalBalance = $variants->sum('total_balance');
            
            // Calculate stock health status (worst-case scenario: if any variant is critical, item is critical)
            $stockHealth = 'healthy'; // Default
            $criticalVariantsCount = 0;
            $lowStockVariantsCount = 0;
            
            foreach ($variants as $variant) {
                $alertLevel = $variant->alert_level ?? 0;
                $variantBalance = $variant->total_balance ?? 0;
                
                // Skip if alert_level is 0 or null (no alert set)
                if ($alertLevel <= 0) {
                    continue;
                }
                
                if ($variantBalance < $alertLevel) {
                    $stockHealth = 'critical';
                    $criticalVariantsCount++;
                } elseif ($variantBalance === $alertLevel && $stockHealth !== 'critical') {
                    $stockHealth = 'low_stock';
                    $lowStockVariantsCount++;
                }
            }
            
            // Add calculated stock fields to the array
            $array['total_stock'] = $totalStock;
            $array['current_stock'] = $totalStock; // Current stock is the same as total in_stock
            $array['projected_stock'] = $totalProjectedStock;
            $array['total_balance'] = $totalBalance;
            $array['current_balance'] = $totalStock; // Current balance might be in_stock
            $array['stock_health'] = $stockHealth;
            $array['critical_variants_count'] = $criticalVariantsCount;
            $array['low_stock_variants_count'] = $lowStockVariantsCount;
            
            // Include variants relationship
            $array['variants'] = InventoryItemVariantResource::collection($variants);
        } else {
            // If variants not loaded, set defaults
            $array['total_stock'] = 0;
            $array['current_stock'] = 0;
            $array['projected_stock'] = 0;
            $array['total_balance'] = 0;
            $array['current_balance'] = 0;
            $array['stock_health'] = 'healthy';
            $array['critical_variants_count'] = 0;
            $array['low_stock_variants_count'] = 0;
        }

        return $array;
    }
}

