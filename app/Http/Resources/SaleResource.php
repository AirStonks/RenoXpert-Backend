<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\PurchaseOrder;
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
            'user' => $this->user,
            'invoices' => $this->invoices,
            'description' => $this->description,
            'total_amount' => $this->total_amount,
            'remaining_amount' => $this->remaining_amount,
            'remaining_percentage' => $this->remaining_percentage,
            // get the sum percentage of status==='paid' invoice of $this->invoices
            'paid_percentage' => $this->invoices->where('status', 'paid')->sum('percentage'),
            'purchase_orders' => PurchaseOrderResource::collection($this->purchaseOrders),
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
