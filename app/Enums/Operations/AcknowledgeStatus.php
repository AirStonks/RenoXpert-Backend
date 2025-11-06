<?php

namespace App\Enums\Operations;

// For reno_progress.rpm_acknowledge_status
enum AcknowledgeStatus: string
{
    case PENDING = 'pending';
    case ACKNOWLEDGED = 'acknowledged';
}

