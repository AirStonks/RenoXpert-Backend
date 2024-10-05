<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'sales_no',
        'description',
        'total_amount',
        'remaining_amount',
        'remaining_percentage',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'sale_id', 'id');
    }
}
