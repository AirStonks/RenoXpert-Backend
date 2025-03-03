<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceItem extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'resource_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'resource_id',
        'item_reference_id',
        'item_reference_type',
        'item_name',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->created_by = auth()->id(); // or your logic to get the user ID
    //     });

    //     static::updating(function ($model) {
    //         $model->updated_by = auth()->id(); // or your logic to get the user ID
    //     });
    // }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id', 'id');
    }

    public function itemReference()
    {
        return $this->morphTo();
    }

    public function userPermissions()
    {
        return $this->belongsToMany(User::class, 'user_item_permission', 'item_id', 'user_id')
            ->withPivot('permission_id')
            ->withTimestamps();
    }
}
