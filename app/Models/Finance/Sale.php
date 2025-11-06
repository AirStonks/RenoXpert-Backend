<?php

namespace App\Models\Finance; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Finance\SalesStatus;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales';
    protected $guarded = [];

    protected $casts = [
        'status' => SalesStatus::class, // Using our new Enum
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the order this sale is linked to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Order::class, 'order_id');
    }

    /**
     * Get the user (staff) who made this sale.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'staff_id');
    }
}
