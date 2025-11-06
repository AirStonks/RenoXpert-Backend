<?php

namespace App\Data\Business;

use App\Data\Foundation\UserData;
use App\Enums\Business\QuotationStatus;
use App\Models\Business\Quotation;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Carbon\Carbon;

// Import the new DTOs
use App\Data\Business\UnitPropertyConfigData;
use App\Data\Business\AddonQuotationData;
use App\Data\Business\QuotationPaymentTermData;


class QuotationData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $quotation_no,
        public readonly string $draft_quotation_no,
        public readonly ?int $compliance_id,
        public readonly QuotationStatus $status,
        public readonly float $total_amount,
        public readonly int $user_id,
        public readonly ?string $property_address_1,
        public readonly ?string $property_address_2,
        public readonly ?string $property_address_city,
        public readonly ?string $property_address_postcode,
        public readonly ?string $property_address_state,
        public readonly ?Carbon $start_date,
        public readonly ?Carbon $end_date,
        public readonly ?Carbon $released_at,
        public readonly ?Carbon $confirmed_at,

        // --- RELATIONSHIPS ---
        public readonly null|Lazy|UserData $user,
        // public readonly null|Lazy|ComplianceData $compliance, 
        /** @var DataCollection<AddonQuotationData>|Lazy|null */
        public readonly null|Lazy|DataCollection $addons,
        public readonly null|Lazy|UnitPropertyConfigData $unitPropertyConfig,

        // --- ADDED RELATIONSHIP ---
        public readonly null|Lazy|QuotationPaymentTermData $paymentTerm,

    ) {}

    public static function rules(): array
    {
        $quotationId = request()->route('quotation')?->id;

        return [
            'quotation_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique(\App\Models\Business\Quotation::class)->ignore($quotationId)
            ],
            'draft_quotation_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique(\App\Models\Business\Quotation::class, 'draft_quotation_no')->ignore($quotationId)
            ],
            'compliance_id' => ['nullable', 'integer', Rule::exists('compliances', 'id')],
            'status' => ['required', Rule::enum(QuotationStatus::class)],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],

            // ... address fields ...
            'property_address_1' => ['nullable', 'string', 'max:255'],
            'property_address_2' => ['nullable', 'string', 'max:255'],
            'property_address_city' => ['nullable', 'string', 'max:255'],
            'property_address_postcode' => ['nullable', 'string', 'max:255'],
            'property_address_state' => ['nullable', 'string', 'max:255'],

            // ... date fields ...
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],

            // ... nested DTOs ...
            'unitPropertyConfig' => ['nullable', 'array'],
            // (You can add specific rules for unitPropertyConfig fields here)

            'addons' => ['nullable', 'array'],
            // (You can add specific rules for addons fields here)

            'paymentTerm' => ['nullable', 'array'],
            // (You can add specific rules for paymentTerm fields here)
        ];
    }
}
