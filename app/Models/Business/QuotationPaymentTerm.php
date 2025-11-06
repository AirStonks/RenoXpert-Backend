<?php

namespace App\Models\Business;

use App\Data\Business\QuotationPaymentTermData;
use App\Enums\Business\InstallmentMethod;
use App\Enums\Business\PaymentTerm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelData\WithData;

class QuotationPaymentTerm extends Model
{
    use HasFactory, SoftDeletes, WithData;

    protected $table = 'quotation_payment_terms';
    protected $dataClass = QuotationPaymentTermData::class;
    protected $guarded = [];

    protected $casts = [
        'payment_term' => PaymentTerm::class,
        'installment_method' => InstallmentMethod::class,
        'installment_amount' => 'decimal:2',
        'rnpl_base_price' => 'decimal:2',
        'reno_sub_base_price' => 'decimal:2',
    ];

    /**
     * Get the main quotation that this config belongs to.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Quotation::class, 'quotation_id');
    }
}