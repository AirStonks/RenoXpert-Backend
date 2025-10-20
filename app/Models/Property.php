<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Property extends Model
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
        'address',
        'street',
        'postcode',
        'city',
        'state',
        'description',
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

    public function propertyRoi()
    {
        return $this->hasOne(PropertyROI::class, 'property_id', 'id');
    }

    /**
     * Get the project status histories for the property.
     */
    public function projectStatusHistories()
    {
        return $this->hasMany(ProjectStatusHistory::class, 'property_id', 'id');
    }
}
