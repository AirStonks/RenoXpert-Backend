<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'permission_name',
        'permission_description',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id(); // or your logic to get the user ID
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id(); // or your logic to get the user ID
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission')
            ->withPivot('resource_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
            ->withPivot('resource_id');
    }

    public function userItems()
    {
        return $this->belongsToMany(User::class, 'user_item_permission')
            ->withPivot('item_id');
    }

    public function roleItems()
    {
        return $this->belongsToMany(Role::class, 'role_item_permission')
            ->withPivot('item_id');
    }
}
