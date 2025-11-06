<?php

namespace App\Models\Purchasing; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;

class ProductSupply extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_supplies';
    protected $guarded = [];
}
