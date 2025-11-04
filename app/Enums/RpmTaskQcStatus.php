<?php

namespace App\Enums;

// Per your instructions, this is a FIXED status list.
enum RpmTaskQcStatus: string
{
    case NOT_STARTED = 'not-started';
    case ACCEPTED = 'accepted';
    case ACCEPTED_WITH_COMMENT = 'accepted-with-comment';
    case TO_RECTIFIED = 'to-rectified'; // Note: Your DB has 'to-rectified'
    case REJECTED = 'rejected';
}
