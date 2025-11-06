<?php

namespace App\Models\System; // Or App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\System\OtpStatus;

class OtpRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'otp_requests';
    protected $guarded = [];

    protected $casts = [
        'status' => OtpStatus::class,
    ];

    /**
     * Get the user this OTP was for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }
}
