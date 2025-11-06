<?php

namespace App\Models\Foundation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles; // <-- IMPORT THE TRAIT

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles; // <-- USE THE TRAIT

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // We cast our ENUM columns for convenience
        'type' => \App\Enums\Foundation\UserType::class, // Assumes you'll create this Enum
        'status' => \App\Enums\Foundation\UserStatus::class, // Assumes you'll create this Enum
        'phone_no_status' => \App\Enums\Foundation\UserPhoneStatus::class, // Assumes you'll create this Enum
    ];

    /**
     * Get all of the contacts for the User.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(\App\Models\Foundation\Contact::class, 'user_id');
    }

    /**
     * Get all of the addresses for the User.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(\App\Models\Foundation\Address::class, 'user_id');
    }

    /**
     * Get the user who created this record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'updated_by');
    }
}

