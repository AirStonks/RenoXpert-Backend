<?php

namespace App\Models\Catalog; // Or App\Models, depending on your structure

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\Catalog\ProductStatus;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'status' => ProductStatus::class, // Using the Enum we created
        'metadata' => 'array',
        'is_package' => 'boolean',
    ];

    /**
     * Get the category this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Catalog\PmCategory::class, 'pm_category_id');
    }

    /**
     * Get the packages this product is a part of.
     * This defines the many-to-many relationship.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Catalog\Package::class, 'product_packages')
            ->using(\App\Models\Catalog\ProductPackage::class) // Tell Laravel to use our pivot model
            ->withPivot([
                'quantity',
                'visibility',
                'included',
                'isOriginal',
                'includeSupply',
                'includeInstall',
                'internal_note'
            ]);
    }

    /**
     * Get the inventory records for this product.
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(\App\Models\Catalog\Inventory::class, 'product_id');
    }

    /**
     * Get the user who created this.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'created_by');
    }
}
