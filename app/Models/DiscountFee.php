<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\AvailabilityStatus;
use App\Enums\DiscountFeeType;

class DiscountFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discount_fees';
    protected $guarded = [];

    protected $casts = [
        'status' => AvailabilityStatus::class,
        'type' => DiscountFeeType::class,
        'value' => 'decimal:2',
    ];

    /**
     * Get the user (staff) who created this.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
