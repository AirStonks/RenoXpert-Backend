<?php

namespace App\Data;

use App\Enums\RenoProgressStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Carbon\Carbon;

class ProjectStatusHistoryData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly RenoProgressStatus $status,
        public readonly Carbon $created_at,
        public readonly null|Lazy|UserData $createdBy,
    ) {}
}