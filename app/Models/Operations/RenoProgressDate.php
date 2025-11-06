<?php

namespace App\Models\Operations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $reno_progress_id
 * @property ?\Illuminate\Support\Carbon $contractual_end_date
 * @property ?\Illuminate\Support\Carbon $contractual_start_date
 * @property ?\Illuminate\Support\Carbon $contractual_p1_start_date
 * @property ?\Illuminate\Support\Carbon $contractual_p1_end_date
 * @property ?\Illuminate\Support\Carbon $contractual_p2_start_date
 * @property ?\Illuminate\Support\Carbon $contractual_p2_end_date
 * @property ?\Illuminate\Support\Carbon $contractual_qc_start_date
 * @property ?\Illuminate\Support\Carbon $contractual_qc_end_date
 * @property ?\Illuminate\Support\Carbon $contractual_pc_start_date
 * @property ?\Illuminate\Support\Carbon $contractual_pc_end_date
 * @property ?\Illuminate\Support\Carbon $contractual_handover_date
 * @property ?\Illuminate\Support\Carbon $contractor_end_date
 * @property ?\Illuminate\Support\Carbon $contractor_start_date
 * @property ?\Illuminate\Support\Carbon $contractor_p1_start_date
 * @property ?\Illuminate\Support\Carbon $contractor_p1_end_date
 * @property ?\Illuminate\Support\Carbon $contractor_p2_start_date
 * @property ?\Illuminate\Support\Carbon $contractor_p2_end_date
 * @property ?\Illuminate\Support\Carbon $contractor_qc_start_date
 * @property ?\Illuminate\Support\Carbon $contractor_qc_end_date
 * @property ?\Illuminate\Support\Carbon $contractor_pc_start_date
 * @property ?\Illuminate\Support\Carbon $contractor_pc_end_date
 * @property ?\Illuminate\Support\Carbon $contractor_handover_date
 * @property ?array $date_management
 */
class RenoProgressDate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reno_progress_dates';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // This table does not have created_at/updated_at

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'contractual_end_date' => 'datetime',
        'contractual_start_date' => 'datetime',
        'contractual_p1_start_date' => 'datetime',
        'contractual_p1_end_date' => 'datetime',
        'contractual_p2_start_date' => 'datetime',
        'contractual_p2_end_date' => 'datetime',
        'contractual_qc_start_date' => 'datetime',
        'contractual_qc_end_date' => 'datetime',
        'contractual_pc_start_date' => 'datetime',
        'contractual_pc_end_date' => 'datetime',
        'contractual_handover_date' => 'datetime',
        'contractor_end_date' => 'datetime',
        'contractor_start_date' => 'datetime',
        'contractor_p1_start_date' => 'datetime',
        'contractor_p1_end_date' => 'datetime',
        'contractor_p2_start_date' => 'datetime',
        'contractor_p2_end_date' => 'datetime',
        'contractor_qc_start_date' => 'datetime',
        'contractor_qc_end_date' => 'datetime',
        'contractor_pc_start_date' => 'datetime',
        'contractor_pc_end_date' => 'datetime',
        'contractor_handover_date' => 'datetime',
        'date_management' => 'array',
    ];

    /**
     * Get the main reno progress record this dates record belongs to.
     */
    public function renoProgress(): BelongsTo
    {
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id');
    }
}
