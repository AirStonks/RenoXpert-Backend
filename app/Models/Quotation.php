<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\PackageStatus; // Re-using this Enum

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotations';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'available', 'unavailable', 'archived'
        'status' => PackageStatus::class,
    ];

    /**
     * Get all the "versions" (OrderQuotations) for this main quote.
     */
    public function orderQuotations(): HasMany
    {
        return $this->hasMany(OrderQuotation::class, 'quotation_id');
    }
}
