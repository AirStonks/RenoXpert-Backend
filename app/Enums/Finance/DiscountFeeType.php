<?php

namespace App\Enums\Finance;

// For the `discount_fees.type` column
enum DiscountFeeType: string
{
    case FEE = 'fee';
    case DISCOUNT = 'discount';
}
