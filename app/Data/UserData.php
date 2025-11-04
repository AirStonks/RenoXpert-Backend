<?php

namespace App\Data; // <-- CORRECTED

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// --- THESE WERE MISSING ---
use App\Enums\UserType;
use App\Enums\UserStatus;
// -------------------------

class UserData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password,
        
        // --- THESE ARE NOW ENUMS, NOT STRINGS ---
        public readonly UserType $type,
        public readonly UserStatus $status,
        // --------------------------------------

        public readonly string $country_code,
        public readonly string $phone_no,
        /** @var DataCollection<ContactData>|Lazy|null */
        public readonly null|Lazy|DataCollection $contacts,
        /** @var DataCollection<AddressData>|Lazy|null */
        public readonly null|Lazy|DataCollection $addresses,
    ) {}

    /**
     * Validation rules.
     */
    public static function rules(): array
    {
        $userId = request()->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique(User::class, 'email')->ignore($userId)
            ],
            'password' => [
                'nullable',
                'required_if:id,null',
                'confirmed', 
                Password::min(8)->mixedCase()->numbers()
            ],
            
            // --- THESE ARE NOW ENUM RULES ---
            'type' => ['required', Rule::enum(UserType::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            // ------------------------------

            'country_code' => ['required', 'string', 'max:10'],
            'phone_no' => ['required', 'string', 'max:20'],
            'contacts' => ['nullable', 'array'],
            'addresses' => ['nullable', 'array'],
        ];
    }
}