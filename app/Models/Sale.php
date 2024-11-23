<?php

namespace App\Models;

use App\Events\SaleStatusUpdated; // Updated event name
use App\Listeners\TriggerCreateRenoProgress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sale extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'sales_no',
        'description',
        'total_amount',
        'remaining_amount',
        'remaining_percentage',
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

            if ($model->isDirty('status') && $model->status === 'partial-paid') {
                // Dispatch the event
                if ($model->renoProgress === null) {
                    event(new SaleStatusUpdated($model));
                }
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'sale_id', 'id');
    }

    public function renoProgress()
    {
        return $this->hasOne(RenoProgress::class, 'sale_id', 'id');
    }
}
