<?php

namespace App\Http\Resources\API;

use App\Models\Foundation\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        $attachments = json_decode($this->attachments);

        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'SKU' => $this->SKU,
            'supplier_name' => $this->supplier_name,
            'pm_category' => $this->pmCategory->name ?? null,
            'type' => $this->type,
            'description' => $this->description,
            'uom' => $this->uom,
            'supply_price' => $this->productSupply->retail_price,
            'install_price' => $this->productInstall->retail_price,
            'color' => $this->color,
            'material' => $this->material,
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'status' => $this->status,
            'attachments' => null,
        ];

        // Include pivot data if it exists
        if ($this->pivot) {
            $array['pivot'] = $this->pivot;
        }

        return $array;
    }

    private function filterProvisioningFields($data)
    {
        if (!isset($data)) {
            return "Invalid data";
        }

        unset(
            $data->id,
            $data->product_id,
            $data->created_by,
            $data->updated_by,
            $data->created_at,
            $data->updated_at,
            $data->deleted_at,
        );

        return $data;
    }
}
