<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KayaHeigForm extends Model
{
    use HasFactory;

    protected $table = 'kaya_heig_forms';

    protected $fillable = [
        'full_name',
        'mobile_number',
        'email',
        'tower',
        'floor',
        'unit_type',
        'units_owned', // New field
        'rental_plan',
        'concerns',
        'support_needed',
        'preferred_contact', 
        'preferred_time',
        'aditional_info',
        'status',
    ];

    protected $casts = [
        'concerns' => 'array',
        'rental_plan' => 'array',
        'support_needed' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
