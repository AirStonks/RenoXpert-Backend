<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\RenoProgressStatus;

class ProjectStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'project_status_histories';
    protected $guarded = [];

    // No `updated_at` timestamp on this table
    public const UPDATED_AT = null;

    protected $casts = [
        'status' => RenoProgressStatus::class,
        'metadata' => 'array',
    ];

    /**
     * Get the parent reno_progress record.
     */
    public function renoProgress(): BelongsTo
    {
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id');
    }

    /**
     * Get the user who triggered this history event.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
