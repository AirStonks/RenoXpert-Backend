<?php

namespace App\Data\Business;

use App\Data\Foundation\UserData;
use App\Enums\Business\OrderStatus;
use App\Models\Business\Order;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;

class OrderData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $order_no,
        public readonly ?OrderStatus $status,
        public readonly ?int $user_id,
        public readonly null|Lazy|UserData $user,
        public readonly null|Lazy|UserData $createdBy,
        /** @var DataCollection<OrderQuotationData>|Lazy|null */
        public readonly null|Lazy|DataCollection $quotations,
    ) {}

    public static function rules(): array
    {
        $orderId = request()->route('order')?->id ?? request('id');

        return [
            'order_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Order::class)->ignore($orderId),
            ],
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    public static function fromModel(Order $order): self
    {
        return new self(
            $order->id,
            $order->order_no,
            $order->status,
            $order->user_id,
            Lazy::whenLoaded('user', fn() => UserData::from($order->user)),
            Lazy::whenLoaded('createdBy', fn() => UserData::from($order->createdBy)),
            Lazy::whenLoaded('quotations', fn() => OrderQuotationData::collection($order->quotations)),
        );
    }
}
