<?php

namespace App\Http\Resources;

use App\Models\Finance\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'item_id' => $this->item_id,
            'payments' => PaymentResource::collection($this->payments),
            'invoice_no' => $this->invoice_no,
            'amount' => $this->amount,
            'discountsData' => $this->discountsData,
            'percentage' => $this->percentage,
            'feesData' => $this->feesData,
            'status' => $this->status,
            'link_status' => $this->link_status,
            'version' => $this->version,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
