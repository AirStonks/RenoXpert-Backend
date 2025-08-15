<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'item_id',
        'item_type',
        'invoice_no',
        'percentage',
        'amount',
        'status',
        'link_status',
        'due_date',
        'version',
        'discountsData',
        'feesData',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'discountsData' => 'array',
        'feesData' => 'array',
        'due_date' => 'datetime',
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

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'item_id', 'id');
    }

    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'item_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'id');
    }
}
