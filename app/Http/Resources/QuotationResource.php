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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_amount' => $this->total_amount,
            'description' => $this->description,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'metadata' => json_decode($this->metadata),
            'packages' => json_decode($this->metadata),
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
