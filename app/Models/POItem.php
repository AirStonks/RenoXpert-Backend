<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POItem extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'po_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_package_id',
        'product_id',
        'product_name',
        'product_desc',
        'sku',
        'qty',
        'uom',
        'supply',
        'install',
        'supply_price',
        'install_price',
        'unit_price',
        'total_price',
        'status',
        'shipping_date',
        'shipped_date',
        'delivery_date',
        'delivered_date',
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

    public function poPackage()
    {
        return $this->belongsTo(POPackage::class, 'po_package_id', 'id');
    }
}
