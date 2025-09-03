<?php

namespace App\Http\Resources\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\API\RPMTaskResource as APIRPMTaskResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RPMJobResource extends JsonResource
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
            'job_category' => $this->job_category,
            'name' => $this->name,
            'tasks' => APIRPMTaskResource::collection($this->rpmTasks),
            'status' => $this->status,
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
