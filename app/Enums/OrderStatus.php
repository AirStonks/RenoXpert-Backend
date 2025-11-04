<?php

namespace App\Enums; 

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case RELEASED = 'released';
    case CONFIRMED = 'confirmed';
    case VOIDED = 'voided';
}