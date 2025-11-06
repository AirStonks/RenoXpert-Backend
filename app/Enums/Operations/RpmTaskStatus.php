<?php

namespace App\Enums\Operations;

// Per your instructions, this is a FIXED status list.
enum RpmTaskStatus: string
{
    case NOT_APPLICABLE = 'not-applicable';
    case PROCUREMENT_DONE = 'procurement-done';
    case PENDING_STOCKS = 'pending-stocks';
    case DELIVERED = 'delivered';
    case PENDING_INSTALLATION = 'pending-installation';
    case IN_PROGRESS = 'in-progress';
    case COMPLETED = 'completed';
    case TO_RECTIFIED = 'to-rectified';
    case REJECTED = 'rejected';
}
