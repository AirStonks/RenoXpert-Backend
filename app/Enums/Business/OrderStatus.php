<?php

namespace App\Enums\Business;

enum OrderStatus: string
{
    case UNRELEASED = 'unreleased';
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case RELEASED = 'released';
    case VOIDED = 'voided';
}
