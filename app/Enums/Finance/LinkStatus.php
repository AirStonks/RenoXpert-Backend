<?php

namespace App\Enums\Finance;

// For the `invoices.link_status` column
enum LinkStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
