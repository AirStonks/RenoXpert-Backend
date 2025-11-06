<?php

namespace App\Data\Property;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Illuminate\Validation\Rule;
use App\Models\Property\Property;

class PropertyData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $address,
        public readonly ?string $street,
        public readonly ?string $postcode,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $description,
        public readonly null|Lazy|PropertyRoiData $propertyRoi,
        /** @var Lazy|ProjectStatusHistoryData[] */
        public readonly null|Lazy|array $projectStatusHistories,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'street' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
