<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ProgressStatus;

class RpmJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rpm_jobs';
    protected $guarded = [];

    protected $casts = [
        'status' => ProgressStatus::class,
        'due_date' => 'date',
    ];

    /**
     * Get the reno_progress this job is for.
     */
    public function renoProgress(): BelongsTo
    {
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id');
    }

    /**
     * Get all tasks for this job.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(RpmTask::class, 'rpm_job_id');
    }
}
