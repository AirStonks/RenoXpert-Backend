<?php

namespace App\Models\System; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'api_keys';
    protected $guarded = [];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user this API key belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }
}
