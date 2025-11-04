<?php

namespace App; // Or App\Data

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Validation\Rule;

class OrderData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $order_no,
        public readonly OrderStatus $status,
        public readonly int $user_id,
        public readonly null|Lazy|UserData $user,
        public readonly null|Lazy|UserData $createdBy,
        /** @var Lazy|OrderQuotationData[] */
        public readonly null|Lazy|array $quotations,
    ) {}

    public static function rules(): array
    {
        return [
            'order_no' => ['required', 'string', 'max:255', Rule::unique(Order::class)],
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ];
    }
}
