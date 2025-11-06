<?php

namespace App\Models\Operations; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\Operations\RenoProgressStatus;
use App\Enums\Operations\AcknowledgeStatus;

class RenoProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reno_progress';
    protected $guarded = [];

    protected $casts = [
        'status' => RenoProgressStatus::class,
        'rpm_acknowledge_status' => AcknowledgeStatus::class,
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order this progress is for.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Order::class, 'order_id');
    }

    /**
     * Get the user (owner) this progress belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }

    /**
     * Get the history of status changes.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(\App\Models\Operations\ProjectStatusHistory::class, 'reno_progress_id');
    }
}
