<?php

namespace App\Models\Business; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Business\OrderStatus;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';
    protected $guarded = [];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    /**
     * Get the user (customer) who this order belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }

    /**
     * Get the user (staff) who created this order.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'created_by');
    }

    /**
     * Get all the quotation versions for this order.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(\App\Models\Business\OrderQuotation::class, 'order_id');
    }
}
