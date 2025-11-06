<?php

namespace App\Enums\Business;

enum QuotationStatus: string
{
    case UNRELEASED = 'unreleased';
    case DRAFT = 'draft';
    case REVIEWING = 'reviewing';
    case REJECTED = 'rejected';
    case RELEASED = 'released';
    case CONFIRMED = 'confirmed';
    case VOIDED = 'voided';
}