<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Enums\AvailabilityStatus;
use App\Enums\DiscountFeeType;
use App\Models\DiscountFee;
use Illuminate\Validation\Rule;

class DiscountFeeData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly DiscountFeeType $type,
        public readonly AvailabilityStatus $status,
        public readonly float $value,
        public readonly ?string $description,
        public readonly null|Lazy|UserData $createdBy,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(DiscountFee::class)],
            'type' => ['required', Rule::enum(DiscountFeeType::class)],
            'status' => ['required', Rule::enum(AvailabilityStatus::class)],
            'value' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }
}
