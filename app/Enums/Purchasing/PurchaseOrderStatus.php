<?php

namespace App\Enums\Purchasing;

// For purchase_orders.status
// This is a custom list based on our standardization
enum PurchaseOrderStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case PARTIAL_COMPLETED = 'partial-completed'; // DB has 'partial-completed'
}
