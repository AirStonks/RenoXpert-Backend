<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTPRequest extends Model
{
    use HasFactory;

    protected $table = 'otp_requests';

    protected $fillable = [
        'mobile',
        'code',
        'status',
        'uuid',
        'sms_id',
        'token',
        'expires_at',
    ];
}
