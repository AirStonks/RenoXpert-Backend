<?php

namespace App; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait; // Your schema has _lft, _rgt columns

class PmCategory extends Model
{
    use HasFactory, SoftDeletes, NodeTrait; // NodeTrait handles the parent/child logic

    protected $table = 'pm_categories';
    protected $guarded = [];

    /**
     * Get the products in this category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'pm_category_id');
    }
}
