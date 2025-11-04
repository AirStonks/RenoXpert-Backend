<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\FinancialStatus;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';
    protected $guarded = [];

    protected $casts = [
        'status' => FinancialStatus::class, // Using our new Enum
        'booked_at' => 'datetime',
    ];

    /**
     * Get the user (owner) who made this booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the payments associated with this booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    /**
     * Get the invoices associated with this booking.
     * (Assuming an invoice might be directly tied to a booking)
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'booking_id');
    }
}
