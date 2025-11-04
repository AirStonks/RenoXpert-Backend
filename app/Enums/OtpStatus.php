<?php

namespace App\Enums;

// For `otp_requests.status`
// We defined this in our standardization plan
enum OtpStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case EXPIRED = 'expired';
}
