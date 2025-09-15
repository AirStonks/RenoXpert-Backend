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
        'description',
        'internal_description',
        'base_amount',
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
}
