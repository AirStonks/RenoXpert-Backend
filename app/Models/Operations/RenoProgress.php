<?php

namespace App\Models\Operations;

use App\Enums\Operations\AcknowledgeStatus;
use App\Enums\Operations\RenoProgressStatus;
use App\Models\Finance\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RenoProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reno_progress';
    protected $guarded = [];

    protected $casts = [
        // REMOVED all 25 date and json columns from here
        'status' => RenoProgressStatus::class,
        'rpm_acknowledge_status' => AcknowledgeStatus::class,
        'completed_at' => 'datetime',
        'defect_updated_at' => 'datetime',
        'permit_updated_at' => 'datetime',
        'sent_to_lark_date' => 'datetime',
        'owner_handover_released_at' => 'datetime',
        'owner_handover_submitted_at' => 'datetime',
    ];

    /**
     * Get the Sale record associated with this reno progress.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    /**
     * NEW: Get the associated date management record.
     */
    public function dates(): HasOne
    {
        return $this->hasOne(RenoProgressDate::class, 'reno_progress_id');
    }

    // ... other relationships like renoSales, rpmJobs, etc.
}
