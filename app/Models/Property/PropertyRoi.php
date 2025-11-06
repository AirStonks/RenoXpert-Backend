<?php

namespace App\Models\Property\Property; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyRoi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'property_rois';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
        'view_enabled' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
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
     * Get the property this ROI belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Property\Property\Property::class, 'property_id');
    }

    /**
     * Get the user (staff) who created this.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'created_by');
    }
}
