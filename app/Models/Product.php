<?php

// app/models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Product extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
        'SKU',
        'type',
        'description',
        'product_retail_price',
        'product_cost_of_good_sold',
        'product_excluded_price',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
}
