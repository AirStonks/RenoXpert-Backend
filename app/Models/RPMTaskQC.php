<?php

namespace App; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database.Eloquent\Relations\BelongsTo;
use App\Enums\RpmTaskQcStatus;

class RpmTaskQc extends Model
{
    use HasFactory;

    protected $table = 'rpm_task_qcs';
    protected $guarded = [];

    protected $casts = [
        'status' => RpmTaskQcStatus::class,
        'metadata' => 'array',
    ];

    /**
     * Get the task this QC check belongs to.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(RpmTask::class, 'rpm_task_id');
    }
}
