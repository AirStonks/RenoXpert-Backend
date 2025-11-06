<?php

namespace App\Data\Business;

use App\Models\Business\Quotation;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class AddonQuotationData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $quotation_id,
        public readonly string $addon_type,
        public readonly string $name,
        public readonly ?string $description,
        public readonly float $price,
        public readonly null|Lazy|QuotationData $quotation,
    ) {}

    /**
     * Validation rules for creating/updating an AddonQuotation.
     */
    public static function rules(): array
    {
        return [
            'quotation_id' => ['required', 'integer', Rule::exists(\App\Models\Business\Quotation::class, 'id')],
            'addon_type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}