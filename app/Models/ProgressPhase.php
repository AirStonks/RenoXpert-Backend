<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressPhase extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'progress_id',
        'name',
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

    public function renoProgress()
    {
        return $this->belongsTo(RenoProgress::class, 'progress_id', 'id');
    }

    public function jobs() {
        return $this->hasMany(PhaseJob::class, 'phase_id', 'id');
    }
}
