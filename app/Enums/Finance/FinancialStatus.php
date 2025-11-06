<?php

namespace App\Enums\Finance;

// This single Enum will be used by Bookings, Invoices, and Payments,
// as we standardized them all to the same list.
enum FinancialStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case VOIDED = 'voided';
    case DRAFT = 'draft';
}
