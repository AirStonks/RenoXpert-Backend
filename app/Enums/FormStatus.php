<?php

namespace App\Enums;

// For `defect_inspection_forms`, `qc_forms`, `owner_inspection_forms`
// We standardized `not_submitted` to `pending`
enum FormStatus: string
{
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
}
