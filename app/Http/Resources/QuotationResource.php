<?php

// app\Http\Resources\QuotationResource.php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $quotationPackagesManyToMany = $quotation->quotationPackages
        // $packages = $quotationPackagesManyToMany->$packages

        // Retrieve the related packages for this quotation
        // $packages = $this->quotationPackages->map(function ($package) {
        //     return [
        //         'id' => $package->id,
        //         'name' => $package->name,
        //         'description' => $package->description,
        //         'price' => $package->price,  // Assuming you have a price attribute
        //         // Add any other relevant package details
        //     ];
        // });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'property' => $this->property ? [
                'id' => $this->property_id,
                'name' => $this->property->name,
                'address' => $this->property->address,
                'street' => $this->property->street,
                'postcode' => $this->property->postcode,
                'city' => $this->property->city,
                'state' => $this->property->state,
                'description' => $this->property->description,
            ] : null, // Return null if property is null
            'total_amount' => $this->total_amount,
            'description' => $this->description,
            'is_ready' => $this->is_ready,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'status' => $this->status,
            // 'metadata' => json_decode($this->metadata),
            'packages' => PackageResource::collection($this->packages),
            // 'packages' => PackageQuoResource::collection($this->packages),
            // 'packages' => $this->packages,
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
