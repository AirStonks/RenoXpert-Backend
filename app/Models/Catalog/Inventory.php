<?php

namespace App\Models\Catalog; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Catalog\InventoryStatus;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventories';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status_' => InventoryStatus::class, // Using the Enum
    ];

    /**
     * Get the product this inventory record is for.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Catalog\Product::class, 'product_id');
    }
}
