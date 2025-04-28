<?php

// app/models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'pm_category_id',
        'supplier_name',
        'SKU',
        'type',
        'description',
        'uom',
        'task_weightage',
        'color',
        'material',
        'width',
        'height',
        'depth',
        'internal_desc',
        'status',
        'attachments',
        'created_by',
        'updated_by',
        'archived_at',
        'archived_by',
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
