<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'product_id',
        'alert_level',
        'total_stock',
        'current_stock',
        'coming_stock',
        'total_available_stock',
        'total_required_stock',
        'utilized_stock',
        'required_stock',
        'current_balance',
        'total_balance',
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
