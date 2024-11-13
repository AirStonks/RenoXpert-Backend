<?php

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefectInspectionFormResource extends JsonResource
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
            'owner_email' => $this->owner_email,
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
            'contractor_name' => $this->contractor_name,
            'contractor_email' => $this->contractor_email,
            'bedroom_count' => $this->bedroom_count,
            'bathroom_count' => $this->bathroom_count,
            'area' => $metadata,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
