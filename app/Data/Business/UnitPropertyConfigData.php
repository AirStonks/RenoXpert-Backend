<?php

namespace App\Data\Business;

use App\Models\Business\Quotation;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class UnitPropertyConfigData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $quotation_id,
        public readonly ?int $bedroom_count,
        public readonly ?int $single_bedroom_count,
        public readonly ?int $queen_bedroom_count,
        public readonly ?int $studio_count,
        public readonly ?int $bathroom_count,
        public readonly bool $include_partition,
        public readonly null|Lazy|QuotationData $quotation,
    ) {}

    /**
     * Validation rules for creating/updating a UnitPropertyConfig.
     */
    public static function rules(): array
    {
        return [
            'quotation_id' => ['required', 'integer', Rule::exists(\App\Models\Business\Quotation::class, 'id')],
            'bedroom_count' => ['nullable', 'integer', 'min:0'],
            'single_bedroom_count' => ['nullable', 'integer', 'min:0'],
            'queen_bedroom_count' => ['nullable', 'integer', 'min:0'],
            'studio_count' => ['nullable', 'integer', 'min:0'],
            'bathroom_count' => ['nullable', 'integer', 'min:0'],
            'include_partition' => ['required', 'boolean'],
        ];
    }
}