<?php

namespace App\Models\Business;

use App\Data\Business\QuotationData;
// Import the new, correct Enum
use App\Enums\Business\QuotationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelData\WithData;

// Import the new model
use App\Models\Business\QuotationPaymentTerm;
use App\Models\Business\UnitPropertyConfig;
use App\Models\Business\AddonQuotation;


class Quotation extends Model
{
    use HasFactory, SoftDeletes, WithData;

    protected $table = 'quotations';
    protected $dataClass = QuotationData::class;
    protected $guarded = [];

    protected $casts = [
        // Use the new QuotationStatus Enum
        'status' => QuotationStatus::class,
        'total_amount' => 'decimal:2',
        // Removed start_date and end_date as they are no longer in the table
        'released_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }

    /**
     * NOTE: This relationship points to a non-existent column 'order_id'
     * in the 'reno_progress' table based on your SQL dump.
     * It should likely be a HasManyThrough relationship via the 'sales' table.
     */
    public function renoProgress(): HasMany
    {
        // Keeping as-is from your file, but recommend reviewing this.
        return $this->hasMany(\App\Models\Operations\RenoProgress::class, 'order_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Models\Finance\Invoice::class, 'item_id')->where('item_type', self::class);
    }

    public function compliance(): BelongsTo
    {
        return $this->belongsTo(Compliance::class, 'compliance_id');
    }

    public function unitPropertyConfig(): HasOne
    {
        return $this->hasOne(\App\Models\Business\UnitPropertyConfig::class, 'quotation_id');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(\App\Models\Business\AddonQuotation::class, 'quotation_id');
    }

    /**
     * Get the payment term configuration for this quotation.
     */
    public function paymentTerm(): HasOne
    {
        return $this->hasOne(\App\Models\Business\QuotationPaymentTerm::class, 'quotation_id');
    }


    // --- DEPRECATED RELATIONSHIPS ---
    /**
     * @deprecated
     */
    public function orderQuotations(): HasMany
    {
        return $this->hasMany(\App\Models\Business\OrderQuotation::class, 'order_id');
    }
}