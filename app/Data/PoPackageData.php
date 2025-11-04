<?php

namespace App\Data;

use App\Enums\ProgressStatus;
use Spatie\LaravelData\Data;

class PoPackageData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $package_id,
        public readonly ProgressStatus $status,
        public readonly int $quantity,
        public readonly float $price,
    ) {}
}