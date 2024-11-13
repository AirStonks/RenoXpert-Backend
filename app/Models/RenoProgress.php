<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenoProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'status',
        'completed_at',
        'created_by',
        'updated_by',
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
