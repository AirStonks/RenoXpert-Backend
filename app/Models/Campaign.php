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
        'metadata',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'thumbnail' => 'array',
        'thumbnail_video' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id(); // or your logic to get the user ID
            $model->updated_by = auth()->id();
        });

        // Only stamp updated_by for authenticated users. The public booking flow
        // (PaymentController) saves the campaign to consume a slot without a user,
        // and must not wipe the last editor.
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    /**
     * Bump updated_at/updated_by after a nested change (package, layout type,
     * agent assignment) that does not otherwise save the campaign row.
     */
    public function touchUpdatedBy()
    {
        if (auth()->check()) {
            $this->updated_by = auth()->id();
        }

        $this->touch();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function visibleToAgents()
    {
        return $this->belongsToMany(User::class, 'agent_campaign_visibility', 'campaign_id', 'user_id')
            ->withTimestamps();
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
