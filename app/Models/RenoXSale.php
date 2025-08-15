<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RenoXSale extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'reno_x_sales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reno_sale_no',
        'status',
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


    public function sales()
    {
        return $this->hasMany(Sale::class, 'reno_sale_id', 'id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'reno_sale_id', 'id');
    }
}
