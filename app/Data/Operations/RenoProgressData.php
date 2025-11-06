<?php

namespace App\Data\Operations;

use App\Enums\Operations\AcknowledgeStatus;
use App\Enums\Operations\RenoProgressStatus;
use App\Models\Operations\RenoProgress;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Illuminate\Validation\Rule;

class RenoProgressData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $sale_id,
        public readonly ?RenoProgressStatus $status,
        public readonly ?AcknowledgeStatus $rpm_acknowledge_status,
        public readonly ?int $resource_id,
        public readonly ?int $permission_id,
        public readonly ?Carbon $completed_at,
        public readonly ?Carbon $defect_updated_at,
        public readonly null|Lazy|RenoProgressDateData $dates,
    ) {}

    public static function fromModel(RenoProgress $renoProgress): self
    {
        return new self(
            $renoProgress->id,
            $renoProgress->sale_id,
            $renoProgress->status,
            $renoProgress->rpm_acknowledge_status,
            $renoProgress->resource_id,
            $renoProgress->permission_id,
            $renoProgress->completed_at,
            $renoProgress->defect_updated_at,
            Lazy::whenLoaded('dates', $renoProgress, function () use ($renoProgress) {
                $dates = $renoProgress->dates;

                return $dates ? RenoProgressDateData::from($dates) : null;
            }),
        );
    }

    public static function rules(): array
    {
        return [
            'sale_id' => ['nullable', 'integer', 'exists:sales,id'],
            'status' => ['nullable', Rule::enum(RenoProgressStatus::class)],
            'rpm_acknowledge_status' => ['nullable', Rule::enum(AcknowledgeStatus::class)],
            'resource_id' => ['nullable', 'integer'],
            'permission_id' => ['nullable', 'integer'],
            'completed_at' => ['nullable', 'date'],
            'defect_updated_at' => ['nullable', 'date'],
        ];
    }
}
