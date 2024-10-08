<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'invoice_no',
        'percentage',
        'amount',
        'status',
        'link_status',
        'due_date',
        'version',
        'discountsData',
        'feesData',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'id');
    }
}
