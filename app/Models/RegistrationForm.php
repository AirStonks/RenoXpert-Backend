<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationForm extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'block',
        'level',
        'unit',
        'layout_type',
        'sqft',
        'quest_1',
        'quest_2',
        'quest_3',
        'quest_4',
        'quest_5',
        'quest_6',
        'quest_7',
        'quest_8',
        'status',
    ];
}
