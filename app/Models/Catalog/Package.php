<?php

namespace App\Models\Catalog; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\Catalog\PackageStatus;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'packages';
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => PackageStatus::class, // Using the Enum we created
    ];

    /**
     * Get the products that are in this package.
     * This defines the many-to-many relationship.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Catalog\Product::class, 'product_packages')
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
     * Get the user who created this.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'created_by');
    }
}
