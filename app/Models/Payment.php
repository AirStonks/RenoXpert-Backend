<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'reference_no',
        'amount',
        'payment_method',
        'currency',
        'description',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
