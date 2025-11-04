<?php

namespace App\Enums;

enum PackageStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case ARCHIVED = 'archived';
}
