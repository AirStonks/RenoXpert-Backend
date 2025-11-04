<?php

namespace App\Enums;

// For the `sales.status` column
enum SalesStatus: string
{
    case ISSUED = 'issued';
    case PARTIAL_PAID = 'partial-paid';
    case FULLY_PAID = 'fully-paid';
}
