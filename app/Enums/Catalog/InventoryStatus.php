<?php

namespace App\Enums\Catalog;

enum InventoryStatus: string
{
    // Based on your previous standardization
    case IN_DEVELOPMENT = 'in_development';
    // You may want to add more, e.g.:
    // case IN_STOCK = 'in_stock';
    // case LOW_STOCK = 'low_stock';
    // case OUT_OF_STOCK = 'out_of_stock';
}

