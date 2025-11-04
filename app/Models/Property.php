<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'properties';
    protected $guarded = [];

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
     * Get the ROI details for this property.
     */
    public function propertyRoi(): HasOne
    {
        return $this->hasOne(PropertyRoi::class, 'property_id', 'id');
    }

    /**
     * Get the project status histories for the property.
     */
    public function projectStatusHistories(): HasMany
    {
        return $this->hasMany(ProjectStatusHistory::class, 'property_id', 'id');
    }

    /**
     * Get the user (staff) who created this.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
