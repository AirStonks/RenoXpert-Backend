<?php

namespace App\Enums\Catalog;

enum ProductStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case ARCHIVED = 'archived';
}
