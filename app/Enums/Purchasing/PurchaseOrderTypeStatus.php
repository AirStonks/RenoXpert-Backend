<?php

namespace App\Enums\Purchasing;

// For purchase_orders.order_status
// This was a fixed list
enum PurchaseOrderTypeStatus: string
{
    case UNRELEASED = 'unreleased';
    case RELEASED = 'released';
}
