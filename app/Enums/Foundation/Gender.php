<?php

namespace App\Enums\Foundation;

// For the 'contacts' table
enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    // Add any other values from your DB
}
