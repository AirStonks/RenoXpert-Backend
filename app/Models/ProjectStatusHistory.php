<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProjectStatusHistory extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reno_progress_id',
        'property_id',
        'unit',
        'status',
        'payment_percentage',
        'snapshot_year',
        'snapshot_week',
        'snapshot_date',
        'snapshot_type',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'snapshot_date' => 'date',
        'payment_percentage' => 'double',
        'snapshot_year' => 'integer',
        'snapshot_week' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Get the reno progress that owns the project status history.
     */
    public function renoProgress()
    {
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id', 'id');
    }

    /**
     * Get the property that owns the project status history.
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }
}
