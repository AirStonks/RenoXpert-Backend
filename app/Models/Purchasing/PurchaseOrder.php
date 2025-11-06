<?php

namespace App\Models\Purchasing; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\Purchasing\PurchaseOrderStatus;
use App\Enums\Purchasing\PurchaseOrderTypeStatus;
use App\Enums\Finance\SalesStatus; // Re-using this Enum

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_orders';
    protected $guarded = [];

    protected $casts = [
        'status' => PurchaseOrderStatus::class,
        'order_status' => PurchaseOrderTypeStatus::class,
        'payment_status' => SalesStatus::class, // Re-using SalesStatus
        'total_amount' => 'decimal:2',
        'metadata' => 'array',
        'due_date' => 'date',
    ];

    /**
     * Get the user (staff) who created this PO.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'created_by');
    }

    /**
     * Get the user (vendor) this PO is for.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'vendor_id');
    }

    /**
     * Get all the line items for this PO.
     */
    public function items(): HasMany
    {
        return $this->hasMany(\App\Models\Purchasing\PoItem::class, 'purchase_order_id');
    }

    /**
     * Get all the package items for this PO.
     */
    public function packages(): HasMany
    {
        return $this->hasMany(\App\Models\Purchasing\PoPackage::class, 'purchase_order_id');
    }
}
