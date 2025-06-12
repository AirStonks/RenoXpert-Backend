<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyROI extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'property_rois';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'thumbnail_title',
        'thumbnail_desc',
        'content',
        'view_enabled',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'content' => 'array',
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
