<?php

namespace App\Models\Lead; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use App\Enums\Lead\RegistrationFormStatus;

class RegistrationForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'registration_forms';
    protected $guarded = [];

    protected $casts = [
        'status' => RegistrationFormStatus::class,
        'metadata' => 'array',
    ];
}
