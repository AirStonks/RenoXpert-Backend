<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'date_management',
        'resource_id',
        'defect_updated_at',
        'permit_updated_at',
        'completed_at',
        'rpm_version',
        'sent_to_lark_date',
        'rpm_acknowledge_status',
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
        'sent_to_lark_date' => 'datetime',
        'defect_updated_at' => 'datetime',
        'permit_updated_at' => 'datetime',
        'completed_at' => 'datetime',
        'date_management' => 'array',
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

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'reno_sales', 'reno_progress_id', 'sale_id')
            ->withPivot('is_main')
            ->withTimestamps();
    }

    public function mainSale()
    {
        return $this->hasOne(Sale::class, 'id', 'sale_id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('reno_sales')
                    ->whereColumn('reno_sales.sale_id', 'sales.id')
                    ->where('reno_sales.reno_progress_id', $this->id)
                    ->where('reno_sales.is_main', true);
            });
    }

    public function progressPhases()
    {
        return $this->hasMany(ProgressPhase::class, 'progress_id', 'id');
    }

    // V3
    public function rpmJobs()
    {
        return $this->hasMany(RPMJob::class, 'reno_progress_id', 'id');
    }

    public function defectInspectionForm()
    {
        return $this->hasOne(DefectInspectionForm::class, 'reno_progress_id', 'id');
    }

    public function keyManagement()
    {
        return $this->hasOne(KeyManagement::class, 'reno_progress_id', 'id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id', 'id');
    }

    public function resourceItem()
    {
        return $this->morphOne(ResourceItem::class, 'item_reference');
    }

    public function itemPermissions()
    {
        return $this->hasOne(ResourceItem::class, 'item_reference_id', 'id')
            ->where('item_reference_type', RenoProgress::class)
            ->with(['userPermissions' => function ($query) {
                $query->withPivot('permission_id')
                    ->withTimestamps();
            }]);
    }
}
