<?php

namespace App\Enums;

// For `registration_forms`
enum RegistrationFormStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
