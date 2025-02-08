<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationPackage extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'package_id',
        'quantity',
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

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'quo_pkg_prods', 'quotation_package_id', 'product_id')
            ->withPivot('quantity', 'visibility', 'quo_pkg_prods.id');
    }


    public function quoProducts()
    {
        return $this->hasMany(QuoPkgProd::class, 'quotation_package_id', 'id');
    }
}
