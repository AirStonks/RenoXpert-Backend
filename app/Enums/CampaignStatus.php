<?php

namespace App\Enums;

// For `campaigns` and `campaign_packages`
// We defined this in our standardization plan
enum CampaignStatus: string
{
    case UNPUBLISHED = 'unpublished';
    case PUBLISHED = 'published';
    case STARTED = 'started';
    case FULLY_REDEEMED = 'fully_redeemed';
    case ENDED = 'ended';
}
