<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTask extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'job_id',
        'product_id',
        'name',
        'qty',
        'priority',
        'task_weightage',
        'area',
        'is_supplied',
        'is_installed',
        'supply_date',
        'install_date',
        'is_defect_form',
        'is_qc_form',
        'is_key_form',
        'is_visible',
        'status',
        'owner_comment',
        'internal_comment',
        'attachments',
        'external_attachment',
        'completed_at',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'supply_date' => 'datetime',
        'install_date' => 'datetime',
        'attachments' => 'array',
        'external_attachment' => 'array',
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

    public function job()
    {
        return $this->belongsTo(PhaseJob::class, 'job_id', 'id');
    }
}
