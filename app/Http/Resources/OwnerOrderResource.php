<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Load the quotations to ensure they are available for sorting
        $this->orderQuotations->load('quotation');

        // Sort orderQuotations by version in descending order
        $orderQuotations = $this->orderQuotations->sortByDesc('version')->values(); // Using values() to reindex the collection

        $latestQuotation = $orderQuotations->first(); // Get the latest quotation from sorted collection

        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'contact_id' => $this->contact_id,
            'contact' => [
                'id' => $this->contact_id,
                'name' => $this->contact->name,
                'email' => $this->contact->email,
                'phone_no' => $this->contact->phone_no,
                'alt_phone_no' => $this->contact->alt_phone_no,
                'race' => $this->contact->race,
                'gender' => $this->contact->gender,
                'nationality' => $this->contact->nationality,
                'description' => $this->contact->description,
            ],
            'property_id' => $this->property_id,
            'property' => [
                'id' => $this->property_id,
                'name' => $this->property->name,
                'address' => $this->property->address,
                'street' => $this->property->street,
                'postcode' => $this->property->postcode,
                'city' => $this->property->city,
                'state' => $this->property->state,
                'description' => $this->property->description,
            ],
            'yay' => 'yay',
            'sale' => new OwnerSaleResource($this->sale),
            'latest_quotation' => $latestQuotation ? $latestQuotation : null,
            'block' => $this->block,
            'floor' => $this->floor,
            'unit_no' => $this->unit_no,
            'total_amount' => $this->total_amount,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
