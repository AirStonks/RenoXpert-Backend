<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Finance\AvailabilityStatus;
use App\Enums\Finance\DiscountFeeType;
use App\Enums\Finance\DiscountFeeValueType; // <-- Import the new Enum

class DiscountFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discount_fees';
    protected $guarded = [];

    protected $casts = [
        'status' => AvailabilityStatus::class,
        'type' => DiscountFeeType::class,
        'value_type' => DiscountFeeValueType::class, // <-- Cast value_type
        'value' => 'decimal:2',                     // <-- Cast value
    ];

    /**
     * Get the user (staff) who created this.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'created_by');
    }
}