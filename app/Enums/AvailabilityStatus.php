<?php

namespace App\Enums;

// We can use this for discount_fees, and also refactor
// ProductStatus and PackageStatus to use this
// to reduce duplicate code.
enum AvailabilityStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case ARCHIVED = 'archived';
}
