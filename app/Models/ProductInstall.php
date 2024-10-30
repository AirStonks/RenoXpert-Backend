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
}
