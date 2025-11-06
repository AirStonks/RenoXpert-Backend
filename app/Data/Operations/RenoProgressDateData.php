<?php

namespace App\Data\Operations;

use App\Models\Operations\RenoProgressDate;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class RenoProgressDateData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $reno_progress_id,
        public readonly ?Carbon $contractual_end_date,
        public readonly ?Carbon $contractual_start_date,
        public readonly ?Carbon $contractual_p1_start_date,
        public readonly ?Carbon $contractual_p1_end_date,
        public readonly ?Carbon $contractual_p2_start_date,
        public readonly ?Carbon $contractual_p2_end_date,
        public readonly ?Carbon $contractual_qc_start_date,
        public readonly ?Carbon $contractual_qc_end_date,
        public readonly ?Carbon $contractual_pc_start_date,
        public readonly ?Carbon $contractual_pc_end_date,
        public readonly ?Carbon $contractual_handover_date,
        public readonly ?Carbon $contractor_end_date,
        public readonly ?Carbon $contractor_start_date,
        public readonly ?Carbon $contractor_p1_start_date,
        public readonly ?Carbon $contractor_p1_end_date,
        public readonly ?Carbon $contractor_p2_start_date,
        public readonly ?Carbon $contractor_p2_end_date,
        public readonly ?Carbon $contractor_qc_start_date,
        public readonly ?Carbon $contractor_qc_end_date,
        public readonly ?Carbon $contractor_pc_start_date,
        public readonly ?Carbon $contractor_pc_end_date,
        public readonly ?Carbon $contractor_handover_date,
        public readonly ?array $date_management
    ) {}

    /**
     * Validation rules.
     */
    public static function rules(): array
    {
        return [
            'reno_progress_id' => ['required', 'integer', 'exists:reno_progress,id'],
            'contractual_end_date' => ['nullable', 'date'],
            'contractual_start_date' => ['nullable', 'date'],
            'contractual_p1_start_date' => ['nullable', 'date'],
            'contractual_p1_end_date' => ['nullable', 'date'],
            'contractual_p2_start_date' => ['nullable', 'date'],
            'contractual_p2_end_date' => ['nullable', 'date'],
            'contractual_qc_start_date' => ['nullable', 'date'],
            'contractual_qc_end_date' => ['nullable', 'date'],
            'contractual_pc_start_date' => ['nullable', 'date'],
            'contractual_pc_end_date' => ['nullable', 'date'],
            'contractual_handover_date' => ['nullable', 'date'],
            'contractor_end_date' => ['nullable', 'date'],
            'contractor_start_date' => ['nullable', 'date'],
            'contractor_p1_start_date' => ['nullable', 'date'],
            'contractor_p1_end_date' => ['nullable', 'date'],
            'contractor_p2_start_date' => ['nullable', 'date'],
            'contractor_p2_end_date' => ['nullable', 'date'],
            'contractor_qc_start_date' => ['nullable', 'date'],
            'contractor_qc_end_date' => ['nullable', 'date'],
            'contractor_pc_start_date' => ['nullable', 'date'],
            'contractor_pc_end_date' => ['nullable', 'date'],
            'contractor_handover_date' => ['nullable', 'date'],
            'date_management' => ['nullable', 'array'],
        ];
    }

    /**
     * Create a DTO from an Eloquent model.
     */
    public static function fromModel(RenoProgressDate $renoProgressDate): self
    {
        return new self(
            $renoProgressDate->id,
            $renoProgressDate->reno_progress_id,
            $renoProgressDate->contractual_end_date,
            $renoProgressDate->contractual_start_date,
            $renoProgressDate->contractual_p1_start_date,
            $renoProgressDate->contractual_p1_end_date,
            $renoProgressDate->contractual_p2_start_date,
            $renoProgressDate->contractual_p2_end_date,
            $renoProgressDate->contractual_qc_start_date,
            $renoProgressDate->contractual_qc_end_date,
            $renoProgressDate->contractual_pc_start_date,
            $renoProgressDate->contractual_pc_end_date,
            $renoProgressDate->contractual_handover_date,
            $renoProgressDate->contractor_end_date,
            $renoProgressDate->contractor_start_date,
            $renoProgressDate->contractor_p1_start_date,
            $renoProgressDate->contractor_p1_end_date,
            $renoProgressDate->contractor_p2_start_date,
            $renoProgressDate->contractor_p2_end_date,
            $renoProgressDate->contractor_qc_start_date,
            $renoProgressDate->contractor_qc_end_date,
            $renoProgressDate->contractor_pc_start_date,
            $renoProgressDate->contractor_pc_end_date,
            $renoProgressDate->contractor_handover_date,
            $renoProgressDate->date_management
        );
    }
}
