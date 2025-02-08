<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductQuoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        $attachments = json_decode($this->product->attachments);

        $array = [
            'quo_pkg_prod_id' => $this->id,
            'id' => $this->product->id,
            'name' => $this->product->name,
            'SKU' => $this->product->SKU,
            'quantity' => $this->quantity,
            'visibility' => $this->visibility,
            'pm_category_id' => $this->product->pm_category_id,
            'pm_category' => $this->product->pmCategory->name ?? null,
            'type' => $this->product->type,
            'description' => $this->product->description,
            'uom' => $this->product->uom,
            'task_weightage' => $this->product->task_weightage,
            'color' => $this->product->color,
            'material' => $this->product->material,
            'width' => $this->product->width,
            'height' => $this->product->height,
            'depth' => $this->product->depth,
            'internal_desc' => $this->product->internal_desc,
            'provisioning' => [
                'supply' => $this->product->productSupply,
                'install' => $this->product->productInstall,
            ],
            'pivot' => [
                'quantity' => $this->quantity,
                'visibility' => $this->visibility,
            ],
            'created_by' => $this->product->created_by,
            'updated_by' => $this->product->updated_by,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'status' => $this->product->status,
            'attachments' => $attachments,
        ];

        // // Include pivot data if it exists
        // if ($this->product->pivot) {
        //     $array['pivot'] = $this->product->pivot;
        // }

        return $array;
    }
}
