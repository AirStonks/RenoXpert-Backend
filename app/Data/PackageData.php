<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Validation\Rule;

class PackageData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly PackageStatus $status,
        /** @var DataCollection<ProductData>|Lazy|null */
        public readonly null|Lazy|DataCollection $products,
    ) {}

    /**
     * Validation rules.
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(PackageStatus::class)],
            'products' => ['nullable', 'array'],
            'products.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            // Add other pivot fields as needed
        ];
    }
}
