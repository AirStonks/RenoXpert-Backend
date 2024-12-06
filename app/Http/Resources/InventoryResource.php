<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->product,
            'alert_level' => $this->alert_level,
            'current_stock' => $this->current_stock,
            'coming_stock' => $this->coming_stock,
            'total_available_stock' => $this->total_available_stock,
            'total_required_stock' => $this->total_required_stock,
            'utilized_stock' => $this->utilized_stock,
            'required_stock' => $this->required_stock,
            'current_balance' => $this->current_balance,
            'total_balance' => $this->total_balance,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
