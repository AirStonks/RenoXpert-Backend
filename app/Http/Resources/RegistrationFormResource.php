<?php

namespace App\Http\Resources;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $salutationOptions = [
            'mr' => 'Mr',
            'ms' => 'Ms',
            'mrs' => 'Mrs',
            'doctor' => 'Doctor',
            'datuk' => 'Datuk',
            'dato' => 'Dato',
            'datin' => 'Datin',
            'datuk_seri' => 'Datuk Seri',
            'dato_seri' => 'Dato Seri',
            'datin_seri' => 'Datin Seri',
        ];

        $property = Property::find($this->property_name);
        $user = User::where('phone_no', $this->phone_no)->first();
        $metadata = json_decode($this->metadata);
        $attachments = json_decode($this->attachments);

        return [
            'id' => $this->id,
            'user' => [
                'id' => $user->id,
                'salutations' => $salutationOptions[$this->salutations] ?? null,
                'name_first' => $this->name_first,
                'name_last' => $this->name_last,
                'name_preferred' => $this->name_preferred,
                'email' => $this->email,
                'country_code' => $this->country_code,
                'phone_no' => $this->phone_no,
                'ic' => $this->ic,
            ],
            'address' => [
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'city' => $this->city,
                'state' => $this->state,
                'postcode' => $this->postcode,
            ],
            'property' => $property ? [
                'id' => $property->id,
                'property_name' => $property->name,
                'block' => $this->block,
                'level' => $this->level,
                'unit' => $this->unit,
                'layout_type' => $this->layout_type,
                'sqft' => $this->sqft,
            ] : null,
            'other_property' => $this->other_property_name ? [
                'property_name' => $this->other_property_name,
                'block' => $this->block,
                'level' => $this->level,
                'unit' => $this->unit,
                'layout_type' => $this->layout_type,
                'sqft' => $this->sqft,
            ] : null,
            'questions' => $metadata->questions,
            'furnishing' => $metadata->furnishing,
            'attachments' => $attachments,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
