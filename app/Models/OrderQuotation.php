<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class OrderQuotation extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_quotations';

    /**
     * The attributes that aren't mass assignable.
     * We use guarded to be safer with mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'bonus' => 'array',
        'total_amount' => 'decimal:2',
        'total_price' => 'decimal:2', // This is the new normalized column
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set created_by and updated_by
        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Get the main order this quote belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Get the parent quotation this version belongs to.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    /**
     * Get all of the normalized packages for this quotation.
     * This links to our NEW `quotation_packages` table.
     */
    public function quotationPackages(): HasMany
    {
        return $this->hasMany(QuotationPackage::class, 'order_quotation_id');
    }
}

