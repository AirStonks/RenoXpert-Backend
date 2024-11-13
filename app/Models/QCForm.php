<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QCForm extends Model
{
    use HasFactory;


    protected $table = 'qc_forms';

    protected $fillable = [
        'id',
        'reno_progress_id',
        'date',
        'time',
        'inspector_first_name',
        'inspector_last_name',
        'inspector_role',
        'contractor_email',
        'property_name',
        'other_property_name',
        'block',
        'level',
        'unit',
        'bedroom_count',
        'bathroom_count',
        'include_commune_living',
        'status',
        'metadata',
        'created_by',
        'updated_by',
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
