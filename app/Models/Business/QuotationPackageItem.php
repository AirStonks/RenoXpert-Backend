<?php

namespace App\Models\Business; // <-- CORRECTED

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationPackageItem extends Model
{
    use HasFactory;
    protected $table = 'quotation_package_items';
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\QuotationPackage::class, 'quotation_package_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Catalog\Product::class, 'product_id');
    }
}