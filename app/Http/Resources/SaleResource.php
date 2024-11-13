<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'sales_no' => $this->sales_no,
            'order_id' => $this->order_id,
            'order' => new OrderResource(Order::find($this->order_id)),
            // 'reno_progress' => new RenoProgressResource($this->renoProgress),
            'reno_progress_id' => $this->renoProgress ? $this->renoProgress->id : null,
            'user_id' => $this->user_id,
            'user' => null,
            'invoices' => $this->invoices,
            'description' => $this->description,
            'total_amount' => $this->total_amount,
            'remaining_amount' => $this->remaining_amount,
            'remaining_percentage' => $this->remaining_percentage,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
