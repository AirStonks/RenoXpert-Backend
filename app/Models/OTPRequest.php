<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OTPRequest extends Model
{
    use SoftDeletes;
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
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];
}
