<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefectInspectionForm extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'id',
        'reno_progress_id',
        'date',
        'time',
        'owner_email',
        'property_name',
        'other_property_name',
        'block',
        'level',
        'unit',
        'contractor_name',
        'contractor_email',
        'bedroom_count',
        'bathroom_count',
        'status',
        'metadata',
        'report_hash',
        'link_status',
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

    public function renoProgress()
    {
        return $this->belongsTo(RenoProgress::class, 'reno_progress_id', 'id');
    }
}
