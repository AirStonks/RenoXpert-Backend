<?php

namespace App\Enums;

// For the `discount_fees.type` column
enum DiscountFeeType: string
{
    case FEE = 'fee';
    case DISCOUNT = 'discount';
}
