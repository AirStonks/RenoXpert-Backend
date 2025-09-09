<?php

namespace App\Http\Resources\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderQuotationResource extends JsonResource
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
            'bonus' => json_decode($this->bonus),
            'packages' => $this->filterMetadataFields(json_decode($this->metadata)),
        ];
    }

    private function filterMetadataFields($data)
    {
        if (!is_array($data)) {
            return "Invalid data";
        }

        foreach ($data as $package) {
            unset(
                $package->available,
                $package->total_price,
                $package->monthly_amount,
                $package->payment_method,
                $package->tenure,
                $package->is_be_powered,
                $package->created_by,
                $package->updated_by,
                $package->created_at,
                $package->updated_at,
                $package->description_internal,
                $package->markup_amount,
                $package->markup_percentage,
                $package->products
            );

            // if (isset($package->products) && is_array($package->products)) {
            //     foreach ($package->products as &$product) {
            //         // Standardize the field name
            //         $product->pivot->is_original = $product->pivot->isOriginal;
            //         $product->pivot->include_supply = $product->pivot->includeSupply;
            //         $product->pivot->include_install = $product->pivot->includeInstall;

            //         unset(
            //             $product->created_by,
            //             $product->updated_by,
            //             $product->created_at,
            //             $product->updated_at,
            //             $product->attachments,
            //             $product->pivot->created_at,
            //             $product->pivot->updated_at,
            //             $product->pivot->isOriginal,
            //             $product->pivot->includeSupply,
            //             $product->pivot->includeInstall,
            //             $product->provisioning->supply->updated_at,
            //             $product->provisioning->supply->created_by,
            //             $product->provisioning->supply->updated_by,
            //             $product->provisioning->install->created_at,
            //             $product->provisioning->install->updated_at,
            //             $product->provisioning->install->created_by,
            //             $product->provisioning->install->updated_by,

            //         );
            //     }
            // }
        }

        return $data;
    }
}
