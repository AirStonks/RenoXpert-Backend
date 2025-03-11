<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

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
        $bonus = json_decode($latestQuotation->bonus, true);

        // Modify the total_amount directly in the array if final_amount exists
        if ($latestQuotation && $this->final_amount) {

            // Log::info($latestQuotation->bonus);
            $latestQuotation->total_amount = $this->final_amount;
        }

        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'ic' => $this->user->ic,
                'country_code' => $this->user->country_code,
                'phone_no' => $this->user->phone_no,
                'address' => $this->user->address,
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
            'sale' => new OwnerSaleResource($this->sale),
            'latest_quotation' => $latestQuotation ? $latestQuotation : null,
            'unit_type' => $this->unit_type,
            'block' => $this->block,
            'floor' => $this->floor,
            'unit_no' => $this->unit_no,
            'include_partition' => $this->include_partition ? true : false,
            'total_amount' => ($this->final_amount ? $this->final_amount - ($bonus ? $bonus['value'] : 0) : $this->total_amount),
            'f_1' => $this->final_amount ? true : false,
            'description' => $this->description,
            'completion_day' => $this->completion_day,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
