<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'internal_description',
        'thumbnail',
        'thumbnail_video',
        'thumbnail_video_url',
        'base_amount',
        'booking_amount',
        'start_date',
        'end_date',
        'published_at',
        'published_by',
        'slot_total',
        'slot_used',
        'slot_remaining',
        'status',
        'visible_to_agents',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'visible_to_agents' => 'boolean',
        'metadata' => 'array',
        'thumbnail' => 'array',
        'thumbnail_video' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id(); // or your logic to get the user ID
        });
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'campaign_id', 'id');
    }

    public function packages()
    {
        return $this->hasMany(CampaignPackage::class, 'campaign_id', 'id');
    }

    public function layoutTypes()
    {
        return $this->hasMany(CampaignLayoutType::class, 'campaign_id', 'id');
    }
}
