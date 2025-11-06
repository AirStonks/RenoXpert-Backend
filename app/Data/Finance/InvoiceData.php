<?php

namespace App\Data\Finance; // <-- CORRECTED

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Enums\Finance\FinancialStatus;
use App\Enums\Finance\LinkStatus;
use App\Models\Finance\Invoice;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Data\Foundation\UserData;

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
        $invoiceId = request()->route('invoice')?->id ?? request('id');

        return [
            'invoice_no' => ['required', 'string', 'max:255', Rule::unique(Invoice::class)->ignore($invoiceId)],
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

    public static function fromModel(Invoice $invoice): self
    {
        return new self(
            $invoice->id,
            $invoice->invoice_no,
            $invoice->status,
            $invoice->link_status,
            $invoice->total_amount !== null ? (float) $invoice->total_amount : 0.0,
            $invoice->due_date,
            $invoice->user_id,
            $invoice->booking_id,
            $invoice->item_id,
            $invoice->item_type,
            Lazy::whenLoaded('user', fn() => UserData::from($invoice->user)),
            Lazy::whenLoaded('payments', fn() => PaymentData::collection($invoice->payments)),
        );
    }
}
