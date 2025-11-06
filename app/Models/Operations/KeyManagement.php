<?php

namespace App\Models\Operations; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Operations\ProgressStatus;

class KeyManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'key_management';
    protected $guarded = [];

    protected $casts = [
        'status' => ProgressStatus::class,
        'metadata' => 'array',
        'handover_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    /**
     * Get the reno_progress this key is for.
     */
    public function renoProgress(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Operations\RenoProgress::class, 'reno_progress_id');
    }
}
