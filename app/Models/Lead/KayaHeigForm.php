<?php

namespace App\Models\Lead; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Lead\InterestFormStatus;

class KayaHeigForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kaya_heig_forms';
    protected $guarded = [];

    protected $casts = [
        'status' => InterestFormStatus::class,
        'metadata' => 'array',
    ];

    /**
     * Get the user this form is linked to (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }
}
