<?php

namespace App\Enums\Business;

enum InstallmentMethod: string
{
    case DYNAMIC = 'dynamic';
    case FIXED = 'fixed';
}