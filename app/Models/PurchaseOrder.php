<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $with = ['poPackages'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_no',
        'sale_id',
        'vendor_id',
        'total_amount',
        'remaining_amount',
        'remaining_percentage',
        'shipping_date',
        'shipped_date',
        'delivery_date',
        'delivered_date',
        'payment_status',
        'order_status',
        'description',
        'internal_note',
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

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function poPackages()
    {
        return $this->hasMany(POPackage::class, 'po_id', 'id');
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'item', 'item_type', 'item_id', 'id')
            ->where('item_type', 'App\Models\PurchaseOrder');
    }
}
