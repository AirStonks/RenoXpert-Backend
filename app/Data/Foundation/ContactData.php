<?php

namespace App\Data\Foundation;

use Spatie\LaravelData\Data;

class ContactData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $phone_no,
        public readonly ?string $alt_phone_no,
        public readonly ?string $email,
        public readonly ?string $gender,
        public readonly ?string $relationship,
    ) {}

    // You can add a static rules() method here
    // if you allow contacts to be created/updated
    // independently of a user.
}
