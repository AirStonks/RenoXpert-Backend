<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AddressData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $address_line_1,
        public readonly ?string $address_line_2,
        public readonly string $city,
        public readonly string $state,
        public readonly string $postcode,
        public readonly string $country,
        public readonly ?bool $is_default,
    ) {}

    // You can add a static rules() method here
    // if you allow addresses to be created/updated
    // independently of a user.
}

