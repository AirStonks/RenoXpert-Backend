<?php

namespace App\Http\Resources\API;

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
            'user' => $this->user ? [
                'id' => $this->user_id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'ic' => $this->user->ic,
                'country_code' => $this->user->country_code,
                'phone_no' => $this->user->phone_no,
                'address' => $this->user->address,
            ] : null,
            'property' => $this->property ? [
                'id' => $this->property_id,
                'name' => $this->property->name,
                'address' => $this->property->address,
                'street' => $this->property->street,
                'postcode' => $this->property->postcode,
                'city' => $this->property->city,
                'state' => $this->property->state,
                'description' => $this->property->description,
            ] : null,
            'bedroom_count' => $this->bedroom_count,
            'single_bedroom_count' => $this->single_bedroom_count,
            'queen_bedroom_count' => $this->queen_bedroom_count,
            'studio_count' => $this->studio_count,
            'bathroom_count' => $this->bathroom_count,
            'include_partition' => $this->include_partition ? true : false,
            'is_progressive_payment' => $this->is_progressive_payment ? true : false,
            'is_be_powered' => $this->is_be_powered ? true : false,
            'installment_method' => $this->installment_method,
            'installment_amount' => $this->installment_amount,
            'be_powered_base_price' => $this->be_powered_base_price,
            'quotation' => $latestQuotation ? new OrderQuotationResource($latestQuotation) : null,
            'unit_type' => $this->unit_type,
            'block' => $this->block,
            'floor' => $this->floor,
            'unit_no' => $this->unit_no,
            'total_amount' => $this->final_amount ? $this->final_amount : $this->total_amount,
            'description' => $this->description,
            'internal_remark' => $this->internal_remark,
            'completion_day' => $this->completion_day,
            'tenure' => $this->tenure,
            'status' => $this->status,
            'released_at' => $this->released_at ? $this->released_at->format('d/m/Y') : null,
            'confirmed_at' => $this->confirmed_at ? $this->confirmed_at->format('d/m/Y') : null,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
