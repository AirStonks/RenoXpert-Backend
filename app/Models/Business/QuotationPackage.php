<?php

namespace App\Models\Business; // <-- CORRECTED

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationPackage extends Model
{
    use HasFactory;
    protected $table = 'quotation_packages';
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function orderQuotation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\OrderQuotation::class, 'order_quotation_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(\App\Models\Business\QuotationPackageItem::class, 'quotation_package_id');
    }
}