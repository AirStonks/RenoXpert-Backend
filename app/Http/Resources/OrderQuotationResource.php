<?php

namespace App\Http\Resources;

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
            'package' => $this->quotation_name,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'quotation' => new QuotationResource($this->quotation),
        ];
    }
}
