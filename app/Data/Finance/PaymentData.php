<?php

namespace App\Data\Finance;

use Spatie\LaravelData\Data;
use App\Enums\Finance\FinancialStatus;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PaymentData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly FinancialStatus $status,
        public readonly float $amount,
        public readonly string $payment_method,
        public readonly int $user_id,
        public readonly ?int $invoice_id,
        public readonly ?int $booking_id,
        public readonly ?Carbon $paid_at,
    ) {}

    /**
     * Validation rules.
     */
    public static function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(FinancialStatus::class)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:30'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'invoice_id' => ['nullable', 'integer', Rule::exists('invoices', 'id')],
            'booking_id' => ['nullable', 'integer', Rule::exists('bookings', 'id')],
            'paid_at' => ['nullable', 'date'],
        ];
    }
}
