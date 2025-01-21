<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderQuotationResource extends JsonResource
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
            'order_id' => $this->order_id,
            'form_id' => $this->order->form_id,
            'quotation_id' => $this->quotation_id,
            'quotation_name' => $this->quotation_name,
            'version' => $this->version,
            'total_amount' => $this->total_amount,
            // Find user
            'created_by' => User::find($this->created_by),
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'bonus' => json_decode($this->bonus),
            'packages' => json_decode($this->metadata),
            // 'quotation' => new QuotationResource($this->quotation),
        ];
    }
}
