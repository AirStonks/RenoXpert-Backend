<?php

namespace App\Data\Finance;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Enums\Finance\AvailabilityStatus;
use App\Enums\Finance\DiscountFeeType;
use App\Enums\Finance\DiscountFeeValueType; // <-- 1. Import the new Enum
use App\Models\Foundation\User;
use App\Models\Finance\DiscountFee;
use Illuminate\Validation\Rule;

// Assuming a UserData DTO exists in this namespace, based on your other DTOs.
use App\Data\Foundation\UserData; 

class DiscountFeeData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly DiscountFeeType $type,
        public readonly DiscountFeeValueType $value_type, // <-- 2. Add value_type
        public readonly ?float $value,                  // <-- 3. Make value nullable (to match DB)
        public readonly AvailabilityStatus $status,
        public readonly ?string $description, // <-- Was in rules, but not constructor. Added.
        public readonly null|Lazy|UserData $createdBy,
    ) {}

    public static function rules(): array
    {
        // Get the ID from the route or request to correctly handle 'unique' rule on updates
        $discountFeeId = request()->route('discount_fee') ? request()->route('discount_fee')->id : null;
        if (empty($discountFeeId) && (request()->has('id'))) {
            $discountFeeId = request('id');
        }

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(\App\Models\Finance\DiscountFee::class)->ignore($discountFeeId)],
            'type' => ['required', Rule::enum(DiscountFeeType::class)],
            'value_type' => ['required', Rule::enum(DiscountFeeValueType::class)], // <-- 4. Add rule for value_type
            'status' => ['required', Rule::enum(AvailabilityStatus::class)],
            'value' => ['nullable', 'numeric', 'min:0'], // <-- 5. Make value nullable
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Create a DTO from an Eloquent model.
     */
    public static function fromModel(DiscountFee $discountFee): self
    {
        return new self(
            $discountFee->id,
            $discountFee->name,
            $discountFee->type,
            $discountFee->value_type, // <-- 6. Add new property
            $discountFee->value,      // <-- 7. Add new property
            $discountFee->status,
            $discountFee->description,
            Lazy::whenLoaded('createdBy', fn() => UserData::from($discountFee->createdBy))
        );
    }
}