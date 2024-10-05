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
        'block',
        'floor',
        'unit_no',
        'total_amount',
        'description',
        'status',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }

    public function orderQuotations()
    {
        return $this->hasMany(OrderQuotation::class, 'order_id', 'id');
    }
}

