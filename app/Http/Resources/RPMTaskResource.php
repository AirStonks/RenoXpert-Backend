<?php

namespace App\Http\Resources;

use App\Models\Foundation\User;
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
            'id' => $this->id,
            'job_id' => $this->job_id,
            'job' => $this->job,
            'task_qc' => $this->task_qc,
            'space_type' => $this->space_type,
            'room_name' => $this->room_name,
            'item_name' => $this->item_name,
            'priority' => $this->priority,
            'task_weightage' => $this->task_weightage,
            'sequence' => $this->sequence,
            'is_visible' => $this->is_visible ? true : false,
            'internal_comment' => $this->internal_comment,
            'owner_comment' => $this->owner_comment,
            'internal_attachments' => $this->internal_attachments,
            'owner_attachments' => $this->owner_attachments,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'qc_task' => new RPMTaskQCResource($this->taskQc),
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
            'created_at' => $this->created_at ? $this->created_at->format('d/m/Y') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y') : null,
        ];
    }
}
