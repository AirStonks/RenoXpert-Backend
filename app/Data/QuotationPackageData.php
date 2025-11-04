<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Models\QuotationPackage;

class QuotationPackageData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?float $price,
        /** @var DataCollection<QuotationPackageItemData>|Lazy|null */
        public readonly null|Lazy|DataCollection $items,
    ) {}

    /**
     * Create a DTO from an Eloquent model.
     */
    public static function fromModel(QuotationPackage $package): self
    {
        return new self(
            $package->id,
            $package->name,
            $package->price,
            Lazy::whenLoaded('items', fn() => QuotationPackageItemData::collection($package->items)),
        );
    }
}
