<?php

namespace App\Models\Lead; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Lead\InterestFormStatus;

class InvestorInterest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'investor_interests';
    protected $guarded = [];

    protected $casts = [
        'status' => InterestFormStatus::class,
        'metadata' => 'array',
    ];

    /**
     * Get the user this interest form is linked to (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }
}
