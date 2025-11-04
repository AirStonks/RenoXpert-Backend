<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use App\Enums\LeadStatus;
use App\Models\CampaignLead;
use Illuminate\Validation\Rule;

class CampaignLeadData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $campaign_id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone_no,
        public readonly LeadStatus $status,
    ) {}

    public static function rules(): array
    {
        return [
            'campaign_id' => ['required', 'integer', Rule::exists('campaigns', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(CampaignLead::class)],
            'phone_no' => ['required', 'string', 'max:20'],
            'status' => ['required', Rule::enum(LeadStatus::class)],
        ];
    }
}
