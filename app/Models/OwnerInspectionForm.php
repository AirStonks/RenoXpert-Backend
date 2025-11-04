<?php

namespace App; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use App\Enums\FormStatus; // Re-using this Enum

class OwnerInspectionForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'owner_inspection_forms';
    protected $guarded = [];

    protected $casts = [
        'status' => FormStatus::class,
    ];
}
