<?php

namespace App\Data\Purchasing;

use App\Enums\Operations\ProgressStatus;
use Spatie\LaravelData\Data;

class PoItemData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $product_id,
        public readonly ProgressStatus $status,
        public readonly int $quantity,
        public readonly float $price,
    ) {}
}