<?php

namespace App\Models\Lead; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use Illuminate\Database.Eloquent\Relations\BelongsTo;
use App\Enums\Lead\CampaignStatus;

class CampaignPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'campaign_packages';
    protected $guarded = [];

    protected $casts = [
        'status' => CampaignStatus::class,
        'metadata' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Get the parent campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lead\Campaign::class, 'campaign_id');
    }

    /**
     * Get the master package this refers to.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Catalog\Package::class, 'package_id');
    }
}
