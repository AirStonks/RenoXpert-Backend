<?php

namespace App; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use App\Enums\LinkStatus; // Re-using 'active'

class RenoXSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reno_x_sales';
    protected $guarded = [];

    protected $casts = [
        'status' => LinkStatus::class,
    ];
}
