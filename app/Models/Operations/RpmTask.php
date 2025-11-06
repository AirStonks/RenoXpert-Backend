<?php

namespace App\Models\Operations; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\Operations\RpmTaskStatus;

class RpmTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rpm_tasks';
    protected $guarded = [];

    protected $casts = [
        'status' => RpmTaskStatus::class,
        'metadata' => 'array',
        'due_date' => 'date',
    ];

    /**
     * Get the parent job this task belongs to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Operations\RpmJob::class, 'rpm_job_id');
    }

    /**
     * Get the QC record for this task.
     */
    public function qc(): HasOne
    {
        return $this->hasOne(\App\Models\Operations\RpmTaskQc::class, 'rpm_task_id');
    }
}
