<?php

namespace App\Models\Catalog; // Or App\Models

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the custom PIVOT model for the 'product_packages' table.
 * It's not a standard model, it's used for the many-to-many relationship.
 */
class ProductPackage extends Pivot
{
    use SoftDeletes;

    protected $table = 'product_packages';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'visibility' => 'boolean',
        'included' => 'boolean',
        'isOriginal' => 'boolean',
        'includeSupply' => 'boolean',
        'includeInstall' => 'boolean',
    ];
}
