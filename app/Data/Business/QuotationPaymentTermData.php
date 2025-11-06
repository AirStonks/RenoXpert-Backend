<?php

namespace App\Data\Business;

use App\Enums\Business\InstallmentMethod;
use App\Enums\Business\PaymentTerm;
use App\Models\Business\Quotation;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class QuotationPaymentTermData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $quotation_id,
        public readonly PaymentTerm $payment_term,
        public readonly ?InstallmentMethod $installment_method,
        public readonly ?float $installment_amount,
        public readonly ?float $rnpl_base_price,
        public readonly ?float $reno_sub_base_price,
        public readonly null|Lazy|QuotationData $quotation,
    ) {}

    /**
     * Validation rules.
     */
    public static function rules(): array
    {
        // Get the payment_term from the input, or from the existing model
        $paymentTerm = request('payment_term');
        if ($paymentTerm instanceof PaymentTerm) {
            $paymentTerm = $paymentTerm->value;
        }

        return [
            'quotation_id' => ['required', 'integer', Rule::exists(\App\Models\Business\Quotation::class, 'id')],
            'payment_term' => ['required', Rule::enum(PaymentTerm::class)],

            // Conditional rules based on payment_term
            'installment_method' => [
                'nullable',
                Rule::requiredIf(fn () => $paymentTerm === PaymentTerm::RNPL->value),
                Rule::enum(InstallmentMethod::class)
            ],
            'installment_amount' => [
                'nullable',
                Rule::requiredIf(fn () => $paymentTerm === PaymentTerm::RNPL->value),
                'numeric',
                'min:0'
            ],
            'rnpl_base_price' => [
                'nullable',
                Rule::requiredIf(fn () => $paymentTerm === PaymentTerm::RNPL->value),
                'numeric',
                'min:0'
            ],
            'reno_sub_base_price' => [
                'nullable',
                Rule::requiredIf(fn () => $paymentTerm === PaymentTerm::RENO_SUB->value),
                'numeric',
                'min:0'
            ],
        ];
    }
}