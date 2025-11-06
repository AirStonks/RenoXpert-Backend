<?php

namespace App\Models\Lead; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use Illuminate\Database.Eloquent\Relations\BelongsTo;
use App\Enums\Lead\FormStatus;

class QcForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'qc_forms';
    protected $guarded = [];

    protected $casts = [
        'status' => FormStatus::class,
        'link_status' => \App\Enums\Finance\LinkStatus::class,
        'metadata' => 'array',
    ];
    
    // You can add relationships here, e.g., to a RenoProgress
}
