<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $latestQuotation = $this->orderQuotations->sortBy('version')->first();
        $latestQuotation->load('quotation');
        
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'contact_id' => $this->contact_id,
            'contact' => [
                'id' => $this->contact_id,
                'name' => $this->contact->name,
                'email' => $this->contact->email,
                'phone_no' => $this->contact->phone_no, // Assuming these fields exist in the Contact model
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
                'address' => $this->property->address, // Assuming these fields exist in the Property model
                'street' => $this->property->street,
                'postcode' => $this->property->postcode,
                'city' => $this->property->city,
                'state' => $this->property->state,
                'description' => $this->property->description,
            ],
            'order_quotations' => $this->orderQuotations,
            'latest_quotation' => $latestQuotation ? $latestQuotation : null,
            'block' => $this->block,
            'floor' => $this->floor,
            'unit_no' => $this->unit_no,
            'description' => $this->description,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
