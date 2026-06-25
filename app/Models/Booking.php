<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'campaign_id',
        'campaign_package_id',
        'user_id',
        'referred_by_user_id',
        'referral_code',
        'booking_no',
        'booking_hash',
        'amount',
        'payment_url',
        'booked_at',
        'expired_at',
        'internal_remark',
        'status',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
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

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function campaignPackage()
    {
        return $this->belongsTo(CampaignPackage::class, 'campaign_package_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id', 'id');
    }
}
