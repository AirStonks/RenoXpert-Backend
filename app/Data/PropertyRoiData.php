<?php

namespace App\Data; // <-- CORRECTED

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Illuminate\Validation\Rule;
use App\Models\PropertyRoi;

class PropertyRoiData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $property_id,
        public readonly ?string $thumbnail_title,
        public readonly ?string $thumbnail_desc,
        public readonly ?array $content,
        public readonly ?bool $view_enabled,
        public readonly null|Lazy|PropertyData $property,
    ) {}

    public static function rules(): array
    {
        return [
            'property_id' => ['required', 'integer', Rule::exists('properties', 'id')],
            'thumbnail_title' => ['nullable', 'string', 'max:255'],
            'thumbnail_desc' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'array'],
            'view_enabled' => ['nullable', 'boolean'],
        ];
    }
}