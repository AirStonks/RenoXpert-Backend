<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'name',
        'priority',
        'is_supplied',
        'is_installed',
        'supply_date',
        'install_date',
        'is_defect_form',
        'is_qc_form',
        'status',
        'owner_comment',
        'internal_comment',
        'attachments',
        'completed_at',
        'created_by',
        'updated_by',
    ];
    
    protected $casts = [
        'supply_date' => 'datetime',
        'install_date' => 'datetime',
        'attachments' => 'array',
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
