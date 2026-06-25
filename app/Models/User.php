<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use SoftDeletes;
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'name_first',
        'name_last',
        'name_preferred',
        'salutations',
        'ic',
        'email',
        'password',
        'country_code',
        'phone_no',
        'type',
        'referral_code',
        'status',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    public static function generateReferralCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (self::where('referral_code', $code)->exists());
        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->referral_code)) {
                $model->referral_code = self::generateReferralCode();
            }
            $model->created_by = auth()->id(); // or your logic to get the user ID
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id(); // or your logic to get the user ID
        });
    }

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withPivot('resource_id');
    }

    public function itemPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_item_permission')
            ->withPivot('item_id');
    }

    /**
     * Get all permissions for a specific resource, including those from roles.
     */
    public function allPermissionsForResource($resourceId)
    {
        $directPermissions = $this->permissions()->wherePivot('resource_id', $resourceId)->get();
        $rolePermissions = collect();

        foreach ($this->roles as $role) {
            $rolePermissions = $rolePermissions->merge(
                $role->permissions()->wherePivot('resource_id', $resourceId)->get()
            );
        }

        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    /**
     * Get all permissions for a specific resource item, including those from roles.
     */
    public function allPermissionsForItem($itemId)
    {
        $directPermissions = $this->itemPermissions()->wherePivot('item_id', $itemId)->get();
        $rolePermissions = collect();

        foreach ($this->roles as $role) {
            $rolePermissions = $rolePermissions->merge(
                $role->itemPermissions()->wherePivot('item_id', $itemId)->get()
            );
        }

        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    /**
     * Check if the user has a specific permission on a resource.
     */
    public function hasPermissionOnResource($permissionName, $resourceId)
    {
        return $this->allPermissionsForResource($resourceId)
            ->contains('permission_name', $permissionName);
    }

    /**
     * Check if the user has a specific permission on a resource item.
     */
    public function hasPermissionOnItem($permissionName, $itemId)
    {
        return $this->allPermissionsForItem($itemId)
            ->contains('permission_name', $permissionName);
    }
}
