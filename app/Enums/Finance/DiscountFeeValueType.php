<?php

namespace App\Enums\Finance;

/**
 * Defines the calculation type for a discount or fee.
 */
enum DiscountFeeValueType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';
}
