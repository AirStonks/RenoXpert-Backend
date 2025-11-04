<?php

namespace App; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use Illuminate\Database.Eloquent\Relations\BelongsTo;
use App\Enums\CampaignStatus;

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
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    /**
     * Get the master package this refers to.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
