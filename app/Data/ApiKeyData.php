<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Illuminate\Validation\Rule;

class ApiKeyData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $user_id,
        public readonly string $key,
        public readonly string $secret,
        public readonly null|Lazy|UserData $user,
    ) {}

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'key' => ['required', 'string', 'max:255', Rule::unique('api_keys')],
            'secret' => ['required', 'string'],
        ];
    }
}
