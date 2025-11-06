<?php

namespace App\Enums\Lead;

// For `registration_forms`
enum RegistrationFormStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
