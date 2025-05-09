<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RPMTaskQC extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'rpm_task_qcs';

    protected $fillable = [
        'task_id',
        'is_visible',
        'internal_comment',
        'owner_comment',
        'internal_attachments',
        'owner_attachments',
        'completed_at',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'internal_attachments' => 'array',
        'owner_attachments' => 'array',
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

    public function task()
    {
        return $this->belongsTo(RPMTask::class, 'task_id', 'id');
    }
}
