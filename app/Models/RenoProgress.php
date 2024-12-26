<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RenoProgress extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'status',
        'contractual_start_date',
        'contractual_end_date',
        'contractual_p1_start_date',
        'contractual_p1_end_date',
        'contractual_p2_start_date',
        'contractual_p2_end_date',
        'contractual_qc_start_date',
        'contractual_qc_end_date',
        'contractual_pc_start_date',
        'contractual_pc_end_date',
        'contractual_handover_date',
        'contractor_start_date',
        'contractor_end_date',
        'contractor_p1_start_date',
        'contractor_p1_end_date',
        'contractor_p2_start_date',
        'contractor_p2_end_date',
        'contractor_qc_start_date',
        'contractor_qc_end_date',
        'contractor_pc_start_date',
        'contractor_pc_end_date',
        'contractor_handover_date',
        'completed_at',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'contractual_start_date' => 'datetime',
        'contractual_end_date' => 'datetime',
        'contractual_p1_start_date' => 'datetime',
        'contractual_p1_end_date' => 'datetime',
        'contractual_p2_start_date' => 'datetime',
        'contractual_p2_end_date' => 'datetime',
        'contractual_qc_start_date' => 'datetime',
        'contractual_qc_end_date' => 'datetime',
        'contractual_pc_start_date' => 'datetime',
        'contractual_pc_end_date' => 'datetime',
        'contractual_handover_date' => 'datetime',
        'contractor_start_date' => 'datetime',
        'contractor_end_date' => 'datetime',
        'contractor_p1_start_date' => 'datetime',
        'contractor_p1_end_date' => 'datetime',
        'contractor_p2_start_date' => 'datetime',
        'contractor_p2_end_date' => 'datetime',
        'contractor_qc_start_date' => 'datetime',
        'contractor_qc_end_date' => 'datetime',
        'contractor_pc_start_date' => 'datetime',
        'contractor_pc_end_date' => 'datetime',
        'contractor_handover_date' => 'datetime',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id(); // or your logic to get the user ID
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id(); // or your logic to get the user ID
        });
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function progressPhases() {
        return $this->hasMany(ProgressPhase::class, 'progress_id', 'id');
    }

    public function defectInspectionForm() {
        return $this->hasOne(DefectInspectionForm::class, 'reno_progress_id', 'id');
    }
}
