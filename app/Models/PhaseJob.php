<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhaseJob extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'phase_id',
        'name',
        'priority',
        'status',
        'completed_at',
        'created_by',
        'updated_by',
        'deleted_at',
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

    public function phase()
    {
        return $this->belongsTo(ProgressPhase::class, 'phase_id', 'id');
    }

    public function tasks() {
        return $this->hasMany(JobTask::class, 'job_id', 'id');
    }
}
