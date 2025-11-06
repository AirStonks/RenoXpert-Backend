<?php

namespace App\Enums\Catalog;

enum PackageStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case ARCHIVED = 'archived';
}
