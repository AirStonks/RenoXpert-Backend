<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationForm extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'id',
        'form_no',
        'salutations',
        'name_first',
        'name_last',
        'name_preferred',
        'email',
        'country_code',
        'phone_no',
        'address_1',
        'address_2',
        'city',
        'state',
        'postcode',
        'ic',
        'property_name',
        'other_property_name',
        'block',
        'level',
        'unit',
        'layout_type',
        'sqft',
        'metadata',
        'attachments',
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
}
