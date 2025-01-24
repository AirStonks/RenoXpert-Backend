<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Termwind\parse;

class OrderResource extends JsonResource
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

        // Remove the latest quotation from the collection
        $orderQuotations = $orderQuotations->slice(1);

        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'user_id' => $this->user_id,
            'form_id' => $this->form_id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'ic' => $this->user->ic,
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
            'bedroom_count' => $this->bedroom_count,
            'bathroom_count' => $this->bathroom_count,
            'latest_quotation' => $latestQuotation ? new OrderQuotationResource($latestQuotation) : null,
            'order_quotations' => OrderQuotationResource::collection($orderQuotations), // This is now sorted in descending order
            // 'latest_quotation' => $latestQuotation ? $latestQuotation : null,
            'block' => $this->block,
            'floor' => $this->floor,
            'unit_no' => $this->unit_no,
            'total_amount' => $this->total_amount,
            'final_amount' => $this->final_amount,
            'description' => $this->description,
            'completion_day' => $this->completion_day,
            'status' => $this->status,
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
