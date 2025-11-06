<?php

namespace App\Data\Business;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Models\Business\OrderQuotation;
use Illuminate\Validation\Rule;

class OrderQuotationData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $order_id,
        public readonly int $quotation_id,
        public readonly ?string $quotation_name,
        public readonly ?int $version,
        public readonly ?float $total_amount,
        public readonly ?array $bonus,
        public readonly ?string $customer_name,
        public readonly ?string $customer_address,
        public readonly ?float $total_price,
        /** @var DataCollection<QuotationPackageData>|Lazy|null */
        public readonly null|Lazy|DataCollection $quotation_packages,
    ) {}

    /**
     * Validation rules for creating/updating.
     */
    public static function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')],
            'quotation_id' => ['required', 'integer', Rule::exists('quotations', 'id')],
            'quotation_name' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'integer'],
            'total_amount' => ['nullable', 'numeric'],
            'bonus' => ['nullable', 'array'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'total_price' => ['nullable', 'numeric'],
            'quotation_packages' => ['nullable', 'array'],
            'quotation_packages.*.name' => ['required_with:quotation_packages', 'string', 'max:255'],
            // Add more nested validation as needed
        ];
    }

    /**
     * Create a DTO from an Eloquent model.
     */
    public static function fromModel(OrderQuotation $orderQuotation): self
    {
        return new self(
            $orderQuotation->id,
            $orderQuotation->order_id,
            $orderQuotation->quotation_id,
            $orderQuotation->quotation_name,
            $orderQuotation->version,
            $orderQuotation->total_amount,
            $orderQuotation->bonus,
            $orderQuotation->customer_name,
            $orderQuotation->customer_address,
            $orderQuotation->total_price,
            Lazy::whenLoaded('quotationPackages', fn() => QuotationPackageData::collection($orderQuotation->quotationPackages)),
        );
    }
}
