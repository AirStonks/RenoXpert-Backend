<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_no',
        'contact_id',
        'property_id',
        'quotation_id',
        'block',
        'floor',
        'unit_no',
        'description',
        'metadata',
    ];
}
