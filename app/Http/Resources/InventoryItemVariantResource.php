<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemVariantResource extends JsonResource
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
            'inventory_item_id' => $this->inventory_item_id,
            'product_id' => $this->product_id,
            'variant_name' => $this->variant_name,
            'type' => $this->type,
            'sku' => $this->sku,
            'description' => $this->description,
            'in_stock' => $this->in_stock,
            'projected_stock' => $this->projected_stock,
            'utilised_stock' => $this->utilised_stock,
            'incoming_stock' => $this->incoming_stock,
            'supply_price' => $this->supply_price,
            'install_price' => $this->install_price,
            'total_balance' => $this->total_balance,
            'alert_level' => $this->alert_level,
            'status' => $this->status,
            'color' => $this->color,
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'material' => $this->material,
            'zone' => $this->zone,
            'shelf_level' => $this->shelf_level,
            'rack_no' => $this->rack_no,
            'created_at' => $this->created_at ? $this->created_at->format('d/m/Y') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y') : null,
        ];

        // Include inventory_item relationship if loaded
        if ($this->relationLoaded('inventoryItem')) {
            $array['inventory_item'] = new InventoryItemResource($this->inventoryItem);
        }

        return $array;
    }
}

