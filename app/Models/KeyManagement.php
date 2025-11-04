<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ProgressStatus;

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
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id');
    }
}
