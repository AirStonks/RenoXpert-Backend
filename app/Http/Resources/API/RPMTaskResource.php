<?php

namespace App\Http\Resources\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RPMTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item_name' => $this->item_name,
            'room_name' => $this->room_name,
            'owner_comment' => $this->owner_comment,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'updated_by' => User::find($this->updated_by)?->name,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y') : null,
        ];
    } 
}
