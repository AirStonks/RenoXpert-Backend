<?php

// Note: Your DB dump did not have values for this.
// I am creating a default. Please update if you have more.
namespace App\Enums;

enum UserPhoneStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
}
