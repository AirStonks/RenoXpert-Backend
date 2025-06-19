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
        'keys_collected',
        'concerns',
        'rental_strategy',
        'support_needed',
        'preferred_contact',
        'preferred_time',
        'status',
    ];

    protected $casts = [
        'concerns' => 'array',
        'rental_strategy' => 'array',
        'support_needed' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
