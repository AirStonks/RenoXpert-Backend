<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Enums\InterestFormStatus;
use App\Models\KayaHeigForm;
use Illuminate\Validation\Rule;

class KayaHeigFormData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone_no,
        public readonly InterestFormStatus $status,
        public readonly ?int $user_id,
        public readonly ?array $metadata,
        public readonly null|Lazy|UserData $user,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(KayaHeigForm::class)],
            'phone_no' => ['required', 'string', 'max:20'],
            'status' => ['required', Rule::enum(InterestFormStatus::class)],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
