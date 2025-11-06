<?php

namespace App\Enums\Business;

enum PaymentTerm: string
{
    case FULL_PAYMENT = 'full_payment';
    case RNPL = 'rnpl';
    case RENO_SUB = 'reno_sub';
}