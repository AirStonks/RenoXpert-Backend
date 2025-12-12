<?php

namespace App\Http\Resources\API\OwnerCS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RenoProgressCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->mainSale->id,
            'property' => [
                'name' => $this->mainSale->order->property->name,
                'block' => $this->mainSale->order->block,
                'floor' => $this->mainSale->order->floor,
                'unit_no' => $this->mainSale->order->unit_no,
            ],
            'status' => $this->status,
        ];
    }
}
