<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = ['mobile', 'expires_at', 'order_ids'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}

