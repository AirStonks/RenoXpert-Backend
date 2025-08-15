<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RenoXSaleResource extends JsonResource
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
            'reno_sale_no' => $this->reno_sale_no,
            'status' => $this->status,
            'sales' => SaleResource::collection($this->sales),
            'purchase_orders' => PurchaseOrderResource::collection($this->purchaseOrders),
            'sale_total_amount' => $this->sales->sum('total_amount'),
            'sale_paid_amount' => $this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * $invoice->percentage;
                });
            })->sum(),
            'sale_paid_percentage' => ($this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * ($invoice->percentage / 100);
                });
            })->sum() / $this->sales->sum('total_amount')) * 100,
            'po_total_amount' => $this->purchaseOrders->sum('total_amount'),
            'po_paid_amount' => $this->purchaseOrders->flatMap(function ($po) {
                return $po->invoices->where('status', 'paid')->map(function ($invoice) use ($po) {
                    return $po->total_amount * $invoice->percentage;
                });
            })->sum(),
            'po_paid_percentage' => $this->purchaseOrders->sum('total_amount') > 0
                ? ($this->purchaseOrders->flatMap(function ($po) {
                    return $po->invoices->where('status', 'paid')->map(function ($invoice) use ($po) {
                        return $po->total_amount * ($invoice->percentage / 100);
                    });
                }))->sum() / $this->purchaseOrders->sum('total_amount') * 100
                : 0,
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
