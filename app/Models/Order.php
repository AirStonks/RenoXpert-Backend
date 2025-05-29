<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    
    use SoftDeletes;
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_no',
        'draft_order_no',
        'user_id',
        'form_id',
        'property_id',
        'unit_type',
        'block',
        'floor',
        'unit_no',
        'bedroom_count',
        'single_bedroom_count',
        'queen_bedroom_count',
        'studio_count',
        'bathroom_count',
        'include_partition',
        'is_progressive_payment',
        'total_amount',
        'final_amount',
        'description',
        'internal_remark',
        'completion_day',
        'status',
        'released_at',
        'confirmed_at',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'released_at' => 'datetime',
        'confirmed_at' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function form()
    {
        return $this->belongsTo(RegistrationForm::class, 'form_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }

    public function orderQuotations()
    {
        return $this->hasMany(OrderQuotation::class, 'order_id', 'id');
    }

    public function sale() {
        return $this->hasOne(Sale::class, 'order_id', 'id');
    }
}

