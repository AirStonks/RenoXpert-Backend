<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RPMJob extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'rpm_jobs';

    protected $fillable = [
        'reno_progress_id',
        'job_category',
        'name',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
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

    public function renoProgress()
    {
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id', 'id');
    }

    public function rpmTasks()
    {
        return $this->hasMany(RPMTask::class, 'job_id', 'id');
    }
}
