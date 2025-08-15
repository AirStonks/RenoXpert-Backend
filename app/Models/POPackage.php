<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class POPackage extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'po_packages';
    protected $with = ['poItems'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_id',
        'package_id',
        'sale_id',
        'name',
        'description',
        'description_internal',
        'category',
        'quantity',
        'total_price',
        'status',
        'created_by',
        'updated_by',
        'archived_at',
        'archived_by',
        'deleted_at',
        'sequence',
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

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }

    public function poItems()
    {
        return $this->hasMany(POItem::class, 'po_package_id', 'id');
    }
}
