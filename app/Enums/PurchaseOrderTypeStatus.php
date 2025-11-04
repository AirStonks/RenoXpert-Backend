<?php

namespace App\Enums;

// For purchase_orders.order_status
// This was a fixed list
enum PurchaseOrderTypeStatus: string
{
    case UNRELEASED = 'unreleased';
    case RELEASED = 'released';
}
