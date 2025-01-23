<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobTaskResource extends JsonResource
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
            'product_id' => $this->product_id,
            'name' => $this->name,
            'qty' => $this->qty,
            'priority' => $this->priority,
            'task_weightage' => $this->task_weightage,
            'area' => $this->area,
            'is_supplied' => $this->is_supplied ? true : false,
            'is_installed' => $this->is_installed ? true : false,
            'supply_date' => $this->supply_date ? $this->supply_date->format('m/d/Y') : null,
            'install_date' => $this->install_date ? $this->install_date->format('m/d/Y') : null,
            'is_defect_form' => $this->is_defect_form ? true : false,
            'is_qc_form' => $this->is_qc_form ? true : false,
            'is_key_form' => $this->is_key_form ? true : false,
            'is_visible' => $this->is_visible ? true : false,
            'status' => $this->status,
            'owner_comment' => $this->owner_comment,
            'internal_comment' => $this->internal_comment,
            'attachments' => $this->attachments,
            'completed_at' => $this->completed_at ? $this->completed_at->format('m/d/Y') : null,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
        ];
    }
}
