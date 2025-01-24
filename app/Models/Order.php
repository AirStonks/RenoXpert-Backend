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
        'user_id',
        'form_id',
        'property_id',
        'block',
        'floor',
        'unit_no',
        'bedroom_count',
        'bathroom_count',
        'total_amount',
        'final_amount',
        'description',
        'status',
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

