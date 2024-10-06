<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'total_price',
    ];
    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_packages', 'package_id', 'product_id')
            ->withPivot('quantity')
            ->withPivot('visibility')
            ->withPivot('included')
            ->withPivot('isOriginal')
            ->withTimestamps();
    }

}
