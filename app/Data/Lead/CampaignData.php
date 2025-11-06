<?php

namespace App\Data\Lead;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Enums\Lead\CampaignStatus;
use App\Models\Lead\Campaign;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CampaignData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly CampaignStatus $status,
        public readonly ?Carbon $start_date,
        public readonly ?Carbon $end_date,
        /** @var DataCollection<CampaignPackageData>|Lazy|null */
        public readonly null|Lazy|DataCollection $packages,
        /** @var DataCollection<CampaignLeadData>|Lazy|null */
        public readonly null|Lazy|DataCollection $leads,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique(\App\Models\Lead\Campaign::class)],
            'status' => ['required', Rule::enum(CampaignStatus::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
