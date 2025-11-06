<?php

namespace App\Http\Resources;

use App\Models\Foundation\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RPMTaskQCResource extends JsonResource
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
            'task_id' => $this->task_id,
            'is_visible' => $this->is_visible ? true : false,
            'internal_comment' => $this->internal_comment,
            'owner_comment' => $this->owner_comment,
            'internal_attachments' => $this->internal_attachments,
            'owner_attachments' => $this->owner_attachments,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
            'created_at' => $this->created_at ? $this->created_at->format('d/m/Y') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y') : null,
        ];
    }
}
