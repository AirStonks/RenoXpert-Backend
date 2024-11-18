<?php

// app/models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'pm_category_id',
        'SKU',
        'type',
        'description',
        'uom',
        'task_weightage',
        'status',
        'created_by',
        'updated_by',
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

    public function pmCategory()
    {
        return $this->belongsTo(PMCategory::class, 'pm_category_id', 'id');
    }

    public function productSupply()
    {
        return $this->hasOne(ProductSupply::class, 'product_id', 'id');
    }

    public function productInstall()
    {
        return $this->hasOne(ProductInstall::class, 'product_id', 'id');
    }
}
