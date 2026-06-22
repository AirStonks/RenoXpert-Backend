<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'layout_type_id',
        'name',
        'description',
        'internal_description',
        'base_amount',
        'booking_amount',
        'start_date',
        'end_date',
        'slot_total',
        'slot_used',
        'slot_remaining',
        'status',
        'metadata',
        'order_id',
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
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'campaign_package_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function layoutType()
    {
        return $this->belongsTo(CampaignLayoutType::class, 'layout_type_id', 'id');
    }
}
