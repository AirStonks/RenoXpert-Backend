<?php

namespace App\Data; // <-- CORRECTED

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Enums\FinancialStatus;
use App\Enums\LinkStatus;
use App\Models\Invoice;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class InvoiceData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $invoice_no,
        public readonly FinancialStatus $status,
        public readonly LinkStatus $link_status,
        public readonly float $total_amount,
        public readonly Carbon $due_date,
        public readonly int $user_id,
        public readonly ?int $booking_id,

        // --- THESE WERE MISSING ---
        public readonly int $item_id,
        public readonly string $item_type,
        // -------------------------

        public readonly null|Lazy|UserData $user,
        /** @var DataCollection<PaymentData>|Lazy|null */
        public readonly null|Lazy|DataCollection $payments,
    ) {}

    /**
     * Validation rules.
     */
    public static function rules(): array
    {
        return [
            'invoice_no' => ['required', 'string', 'max:255', Rule::unique(Invoice::class)],
            'status' => ['required', Rule::enum(FinancialStatus::class)],
            'link_status' => ['required', Rule::enum(LinkStatus::class)],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'booking_id' => ['nullable', 'integer', Rule::exists('bookings', 'id')],
            
            // --- THESE RULES ARE NOW VALID ---
            'item_id' => ['required', 'integer'],
            'item_type' => ['required', 'string'],
            // -------------------------------
        ];
    }
}