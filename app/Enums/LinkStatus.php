<?php

namespace App\Enums;

// For the `invoices.link_status` column
enum LinkStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
