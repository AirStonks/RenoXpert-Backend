<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeyManagement extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'reno_progress_id',
        'date_received_key',
        'date_posted',
        'pic_name',
        'status',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'date_received_key' => 'datetime',
        'date_posted' => 'datetime',
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

    public function renoProgress()
    {
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id', 'id');
    }

}
