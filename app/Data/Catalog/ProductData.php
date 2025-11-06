<?php

namespace App\Data\Catalog;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Enums\Catalog\ProductStatus;
use App\Models\Catalog\Product;
use Illuminate\Validation\Rule;

class ProductData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ProductStatus $status,
        public readonly string $type,
        public readonly ?string $sku,
        public readonly ?string $description,
        public readonly ?float $price,
        public readonly ?float $cost,
        public readonly ?int $pm_category_id,
        // We can include the category name
        public readonly null|Lazy|string $category_name,
    ) {}

    /**
     * Validation rules.
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ProductStatus::class)],
            'type' => ['required', 'string', 'max:50'],
            'sku' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'pm_category_id' => ['nullable', 'integer', Rule::exists('pm_categories', 'id')],
        ];
    }

    /**
     * Create a DTO from an Eloquent model.
     */
    public static function fromModel(Product $product): self
    {
        return new self(
            $product->id,
            $product->name,
            $product->status,
            $product->type,
            $product->sku,
            $product->description,
            $product->price,
            $product->cost,
            $product->pm_category_id,
            Lazy::whenLoaded('category', fn() => $product->category->name),
        );
    }
}
