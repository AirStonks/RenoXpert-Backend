<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignLayoutType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'name',
        'description',
        'sort',
        'rental_projection',
        'roi_calculator',
        'layout_thumbnail',
        'rendering_images',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'rental_projection' => 'array',
        'roi_calculator' => 'array',
        'layout_thumbnail' => 'array',
        'rendering_images' => 'array',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function packages()
    {
        return $this->hasMany(CampaignPackage::class, 'layout_type_id', 'id');
    }
}
