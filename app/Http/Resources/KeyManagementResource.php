<?php

namespace App\Http\Resources;

use App\Models\Foundation\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KeyManagementResource extends JsonResource
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
            'reno_progress_id' => $this->reno_progress_id,
            'date_received_key' => $this->date_received_key ? Carbon::parse($this->date_received_key)->format('Y-m-d') : null,
            'date_posted' => $this->date_posted ? Carbon::parse($this->date_posted)->format('Y-m-d') : null,
            'pic_name' => $this->pic_name,
            'status' => $this->status,
            'metadata' => json_decode($this->metadata),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'created_by' => User::find($this->created_by),
            'updated_by' => User::find($this->updated_by),
        ];
    }
}
