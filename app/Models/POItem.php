<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ProgressStatus; // Re-using this Enum

class PoItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'po_items';
    protected $guarded = [];

    protected $casts = [
        'status' => ProgressStatus::class, // Re-using ProgressStatus
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the parent PO this item belongs to.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the master product this PO item refers to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
