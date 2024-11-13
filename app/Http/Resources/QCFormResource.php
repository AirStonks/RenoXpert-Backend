<?php

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QCFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $property = Property::find($this->property_name);
        $metadata = json_decode($this->metadata);

        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'inspector_first_name' => $this->inspector_first_name,
            'inspector_last_name' => $this->inspector_last_name,
            'inspector_role' => $this->inspector_role,
            'contractor_email' => $this->contractor_email,
            'property' => $property ? [
                'id' => $property->id,
                'property_name' => $property->name,
                'block' => $this->block,
                'level' => $this->level,
                'unit' => $this->unit,
            ] : null,
            'other_property' => $this->other_property_name ? [
                'property_name' => $this->other_property_name,
                'block' => $this->block,
                'level' => $this->level,
                'unit' => $this->unit,
            ] : null,
            'bedroom_count' => $this->bedroom_count,
            'bathroom_count' => $this->bathroom_count,
            'include_commune_living' => $this->include_commune_living === 1 ? true : false,
            'area' => $metadata,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
