<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorInterest extends Model
{
    use HasFactory;

    protected $table = 'investor_interests';

    protected $fillable = [
        'full_name',
        'mobile_number',
        'email',
        'property_name',
        'unit_type',
        'units_owned', // New field
        'keys_collected',
        'concerns',
        'rental_strategy',
        'expected_rental_return', // New field
        'investment_goals', // New field
        'support_needed',
        'preferred_contact',
        'preferred_time',
        'status',
    ];

    protected $casts = [
        'concerns' => 'array',
        'rental_strategy' => 'array',
        'support_needed' => 'array',
        'investment_goals' => 'array', // New field
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
