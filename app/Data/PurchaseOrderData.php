<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Enums\PurchaseOrderStatus;
use App\Enums\PurchaseOrderTypeStatus;
use App\Enums\SalesStatus;
use App\Models\PurchaseOrder;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PurchaseOrderData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $po_no,
        public readonly PurchaseOrderStatus $status,
        public readonly PurchaseOrderTypeStatus $order_status,
        public readonly SalesStatus $payment_status,
        public readonly int $vendor_id,
        public readonly float $total_amount,
        public readonly ?Carbon $due_date,
        public readonly null|Lazy|UserData $vendor,
        /** @var DataCollection<PoItemData>|Lazy|null */
        public readonly null|Lazy|DataCollection $items,
        /** @var DataCollection<PoPackageData>|Lazy|null */
        public readonly null|Lazy|DataCollection $packages,
    ) {}

    public static function rules(): array
    {
        return [
            'po_no' => ['required', 'string', 'max:255', Rule::unique(PurchaseOrder::class)],
            'status' => ['required', Rule::enum(PurchaseOrderStatus::class)],
            'order_status' => ['required', Rule::enum(PurchaseOrderTypeStatus::class)],
            'payment_status' => ['required', Rule::enum(SalesStatus::class)],
            'vendor_id' => ['required', 'integer', Rule::exists('users', 'id')], // Assumes vendor is a User
            'total_amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],

            // Rules for creating items/packages with the PO
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required_with:items', 'integer', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.price' => ['required_with:items', 'numeric', 'min:0'],

            'packages' => ['nullable', 'array'],
            'packages.*.package_id' => ['required_with:packages', 'integer', Rule::exists('packages', 'id')],
            'packages.*.quantity' => ['required_with:packages', 'integer', 'min:1'],
            'packages.*.price' => ['required_with:packages', 'numeric', 'min:0'],
        ];
    }
}
