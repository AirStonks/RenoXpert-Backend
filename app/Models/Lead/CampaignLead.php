<?php

namespace App\Models\Lead; // Or App\Models

use Illuminate\Database.Eloquent\Factories\HasFactory;
use Illuminate\Database.Eloquent\Model;
use Illuminate\Database.Eloquent\SoftDeletes;
use Illuminate\Database.Eloquent\Relations\BelongsTo;
use App\Enums\Lead\LeadStatus;

class CampaignLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'campaign_leads';
    protected $guarded = [];

    protected $casts = [
        'status' => LeadStatus::class,
        'metadata' => 'array',
    ];

    /**
     * Get the parent campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lead\Campaign::class, 'campaign_id');
    }

    /**
     * Get the user (owner) this lead converted into (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Foundation\User::class, 'user_id');
    }
}
