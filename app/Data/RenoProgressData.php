<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use App\Enums\RenoProgressStatus;
use App\Enums\AcknowledgeStatus;
use App\Models\RenoProgress;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class RenoProgressData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $order_id,
        public readonly int $user_id,
        public readonly RenoProgressStatus $status,
        public readonly AcknowledgeStatus $rpm_acknowledge_status,
        public readonly ?Carbon $started_at,
        public readonly ?Carbon $completed_at,
        public readonly null|Lazy|OrderData $order, // Assumes OrderData DTO exists
        public readonly null|Lazy|UserData $user,
        /** @var DataCollection<ProjectStatusHistoryData>|Lazy|null */
        public readonly null|Lazy|DataCollection $statusHistories,
    ) {}

    public static function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'status' => ['required', Rule::enum(RenoProgressStatus::class)],
            'rpm_acknowledge_status' => ['required', Rule::enum(AcknowledgeStatus::class)],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }
}
