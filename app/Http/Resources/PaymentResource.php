<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PaymentResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'transaction_no' => $this->transaction_no,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'payment_channel' => $this->payment_channel,
            'payment_date' => Carbon::parse($this->payment_date)->format('Y-m-d'),
            'bank' => $this->bank,
            'receiving_account' => $this->receiving_account,
            'remark' => $this->remark,
            'currency' => $this->currency,
            'description' => $this->description,
            'status' => $this->status,
            'attachments' => $this->attachments ? json_decode($this->attachments, true) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
