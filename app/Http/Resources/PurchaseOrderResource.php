<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    protected $includeItems;

    /**
     * Constructor to accept the $includeItems parameter.
     *
     * @param mixed $resource
     * @param bool $includeItems
     */
    public function __construct($resource, $includeItems = false)
    {
        parent::__construct($resource);
        $this->includeItems = $includeItems;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // includeItems = true will return the items as well
        $data = [
            'id' => $this->id,
            'po_no' => $this->po_no,
            'sale_id' => $this->sale_id,
            'sale' => $this->sale,
            'vendor_id' => $this->vendor_id,
            'vendor' => $this->vendor,
            'total_amount' => $this->total_amount,
            'shipping_date' => $this->shipping_date ? $this->shipping_date->format('d/m/Y') : null,
            'shipped_date' => $this->shipped_date ? $this->shipped_date->format('d/m/Y') : null,
            'delivery_date' => $this->delivery_date ? $this->delivery_date->format('d/m/Y') : null,
            'delivered_date' => $this->delivered_date ? $this->delivered_date->format('d/m/Y') : null,
            'payment_status' => $this->payment_status,
            'order_status' => $this->order_status,
            'description' => $this->description,
            'internal_note' => $this->internal_note,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Conditionally include the items based on $includeItems flag
        if ($this->includeItems) {
            $data['items'] = $this->poItems;
        }

        return $data;
    }
}
