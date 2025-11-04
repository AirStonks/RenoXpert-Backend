<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Enums\PackageStatus;
use Illuminate\Validation\Rule;

class QuotationData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $quo_no,
        public readonly PackageStatus $status,
        
        /** @var Lazy|OrderQuotationData[] */
        public readonly null|Lazy|array $orderQuotations,
    ) {}

    public static function rules(): array
    {
        return [
            'quo_no' => ['required', 'string', 'max:30'],
            'status' => ['required', Rule::enum(PackageStatus::class)],
        ];
    }
}
