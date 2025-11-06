<?php

namespace App\Enums\Operations;

// This single Enum will be used by key_managements and rpm_jobs,
// as we standardized them to the same list.
enum ProgressStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
}
