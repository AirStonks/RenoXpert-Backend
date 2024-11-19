<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInstall extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'retail_price',
        'cogs',
        'excluded_price',
        'status',
        'created_by',
        'updated_by',
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
}
