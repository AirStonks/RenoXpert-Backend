<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use App\Models\QuotationPackageItem;

class QuotationPackageItemData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $product_id,
        public readonly string $name,
        public readonly int $quantity,
        public readonly ?float $unit_price,
    ) {}

    /**
     * Create a DTO from an Eloquent model.
     */
    public static function fromModel(QuotationPackageItem $item): self
    {
        return new self(
            $item->id,
            $item->product_id,
            $item->name,
            $item->quantity,
            $item->unit_price,
        );
    }
}
