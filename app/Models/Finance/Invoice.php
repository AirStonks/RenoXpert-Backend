<?php

namespace App\Models\Finance; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Enums\Finance\FinancialStatus;
use App\Enums\Finance\LinkStatus;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoices';
    protected $guarded = [];

    protected $casts = [
        'status' => FinancialStatus::class, // Using our new Enum
        'link_status' => LinkStatus::class, // Using our new Enum
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Get the user (owner) this invoice is for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }

    /**
     * Get the booking this invoice is for (if any).
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Finance\Booking::class, 'booking_id');
    }

    /**
     * Get all payments made for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Finance\Payment::class, 'invoice_id');
    }

    /**
     * Get the parent item this invoice is for (polymorphic).
     * This could be an Order, a Booking, etc.
     */
    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
