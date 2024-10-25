<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
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
    ];
}
