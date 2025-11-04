<?php

namespace App; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use Illuminate\Database.Eloquent\Relations\BelongsTo;
use App\Enums\FormStatus;
use App\Enums\LinkStatus; // Re-using this Enum

class DefectInspectionForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'defect_inspection_forms';
    protected $guarded = [];

    protected $casts = [
        'status' => FormStatus::class,
        'link_status' => LinkStatus::class, // Re-using
        'metadata' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the order this form is for.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the user (owner) who this form belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
