<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Package extends Model
{
    use SoftDeletes;
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
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id(); // or your logic to get the user ID
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id(); // or your logic to get the user ID
        });
    }
    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_packages', 'package_id', 'product_id')
            ->withPivot('quantity')
            ->withPivot('visibility')
            ->withPivot('included')
            ->withPivot('isOriginal')
            ->withPivot('internal_note')
            ->withPivot('includeSupply')
            ->withPivot('includeInstall')
            ->withTimestamps();
    }

}
