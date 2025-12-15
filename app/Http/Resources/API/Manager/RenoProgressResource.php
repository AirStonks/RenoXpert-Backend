<?php

namespace App\Http\Resources\API\Manager;

use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class RenoProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $resourceItem = ResourceItem::where('resource_id', $this->resource_id)
        //     ->where('item_reference_id', $this->id)
        //     ->first();

        // $oldRenoProgress = RenoProgress::where('sale_id', $this->sale_id)->onlyTrashed()->first();

        $data = [
            'id' => $this->id,
            'sale_id' => $this->mainSale->id,
            'user_uuid' => $this->mainSale->user->uuid,
            'property' => [
                'name' => $this->mainSale->order->property->name,
                'block' => $this->mainSale->order->block,
                'floor' => $this->mainSale->order->floor,
                'unit_no' => $this->mainSale->order->unit_no,
            ],
            // 'sale' => new SaleResource($this->mainSale),
            // 'defect_inspection_form' => $this->defectInspectionForm ? new DefectInspectionFormResource($this->defectInspectionForm) : null,
            // 'key_management' => $this->keyManagement ? new KeyManagementResource($this->keyManagement) : null,
            'date_management' => $this->date_management,
            'status' => $this->status,
            'total_amount' => round($this->sales->sum('total_amount'), 2),
            'paid_amount' => round($this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * $invoice->percentage;
                });
            })->sum(), 2),
            'remaining_percentage' => round((1 - $this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * ($invoice->percentage / 100);
                });
            })->sum() / $this->sales->sum('total_amount')) * 100, 2),
            'paid_percentage' => round(($this->sales->flatMap(function ($sale) {
                return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                    return $sale->total_amount * ($invoice->percentage / 100);
                });
            })->sum() / $this->sales->sum('total_amount')) * 100, 2),
            'completion' => $this->calculateV3Completion(),
            'completed_at' => $this->completed_at?->format('d/m/Y'),
            'defect_updated_at' => $this->defect_updated_at?->format('d/m/Y'),
            'permit_updated_at' => $this->permit_updated_at?->format('d/m/Y'),
            'owner_handover_released_at' => $this->owner_handover_released_at,
            'owner_handover_submitted_at' => $this->owner_handover_submitted_at,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];

        return $data;
    }

    private function calculateV3Completion(): array
    {
        // Define status weightage mapping
        $statusWeightage = [
            'not-started' => 0.0,
            'pending' => 0.25,
            'in-progress' => 0.5,
            'completed' => 1.0,
        ];

        // Fetch all jobs with their tasks
        $jobs = $this->rpmJobs;
        $phaseCompletions = [];
        $sumPhaseCompletions = 0;

        // Phase definitions
        // Pre Purchase: always 100%
        // Pending VP: RPM Tasks with rpm_jobs->job_category is 'vp'
        // Under Defect: RPM Tasks with rpm_jobs->job_category is 'defect' and 'permit'
        // P1: RPM Tasks with task_phase is 'p1'
        // P2A: RPM Tasks with task_phase is 'p2a'
        // P2B: RPM Tasks with task_phase is 'p2b'
        // Scheduled Handover: RPM Tasks with rpm_jobs->job_category is 'scheduled_handover'
        // Successful Handover: RPM Tasks with task_phase is 'successful_handover'
        // Onboarded: RPM Tasks with task_phase is 'onboarded'

        // Pre Purchase: always 100%
        $phaseCompletions[] = [
            'phase_name' => 'Pre Purchase',
            'completion_percentage' => 1.0,
        ];
        $sumPhaseCompletions += 100;

        // Pending VP: Tasks with job_category 'vp'
        $vpTasks = collect();
        foreach ($jobs->where('job_category', 'vp') as $job) {
            $vpTasks = $vpTasks->merge($job->rpmTasks);
        }
        $vpCompletion = $this->calculatePhaseCompletion($vpTasks, $statusWeightage);
        $phaseCompletions[] = [
            'phase_name' => 'Pending VP',
            'completion_percentage' => $vpCompletion,
        ];
        $sumPhaseCompletions += $vpCompletion * 100;

        // Under Defect: Tasks with job_category 'defect' and 'permit'
        $defectPermitTasks = collect();
        foreach (
            $jobs->filter(function ($job) {
                return in_array($job->job_category, ['defect', 'permit']);
            }) as $job
        ) {
            $defectPermitTasks = $defectPermitTasks->merge($job->rpmTasks);
        }
        $defectPermitCompletion = $this->calculatePhaseCompletion($defectPermitTasks, $statusWeightage);
        $phaseCompletions[] = [
            'phase_name' => 'Under Defect',
            'completion_percentage' => $defectPermitCompletion,
        ];
        $sumPhaseCompletions += $defectPermitCompletion * 100;

        // P1: Tasks with task_phase 'p1' (excluding 'not-available' status)
        $p1Tasks = collect();
        foreach ($jobs as $job) {
            $p1Tasks = $p1Tasks->merge($job->rpmTasks->where('task_phase', 'p1')->where('status', '!=', 'not-applicable'));
        }
        $p1Completion = $this->calculatePhaseCompletion($p1Tasks, $statusWeightage);
        $phaseCompletions[] = [
            'phase_name' => 'P1',
            'completion_percentage' => $p1Completion,
        ];
        $sumPhaseCompletions += $p1Completion * 100;

        // P2A: Tasks with task_phase 'p2a' (excluding 'not-available' status)
        $p2aTasks = collect();
        foreach ($jobs as $job) {
            $p2aTasks = $p2aTasks->merge($job->rpmTasks->where('task_phase', 'p2a')->where('status', '!=', 'not-applicable'));
        }
        $p2aCompletion = $this->calculatePhaseCompletion($p2aTasks, $statusWeightage);
        $phaseCompletions[] = [
            'phase_name' => 'P2A',
            'completion_percentage' => $p2aCompletion,
        ];
        $sumPhaseCompletions += $p2aCompletion * 100;

        // P2B: Tasks with task_phase 'p2b' (excluding 'not-available' status)
        $p2bTasks = collect();
        foreach ($jobs as $job) {
            $p2bTasks = $p2bTasks->merge($job->rpmTasks->where('task_phase', 'p2b')->where('status', '!=', 'not-applicable'));
        }
        $p2bCompletion = $this->calculatePhaseCompletion($p2bTasks, $statusWeightage);
        $phaseCompletions[] = [
            'phase_name' => 'P2B',
            'completion_percentage' => $p2bCompletion,
        ];
        $sumPhaseCompletions += $p2bCompletion * 100;

        // Scheduled Handover: Tasks with job_category 'scheduled_handover'
        $scheduledHandoverCompletion = $this->calculateScheduledHandoverCompletion();
        $phaseCompletions[] = [
            'phase_name' => 'Scheduled Handover',
            'completion_percentage' => $scheduledHandoverCompletion,
        ];
        $sumPhaseCompletions += $scheduledHandoverCompletion * 100;

        // Successful Handover: Tasks with task_phase 'successful_handover'
        $successfulHandoverCompletion = $this->calculateSuccessfulHandoverCompletion();
        $phaseCompletions[] = [
            'phase_name' => 'Successful Handover',
            'completion_percentage' => $successfulHandoverCompletion,
        ];
        $sumPhaseCompletions += $successfulHandoverCompletion * 100;

        // Onboarded: Tasks with task_phase 'onboarded'
        $onboardedCompletion = $this->status === 'onboarded' ? 1 : 0;
        $phaseCompletions[] = [
            'phase_name' => 'Onboarded',
            'completion_percentage' => $onboardedCompletion,
        ];
        $sumPhaseCompletions += $onboardedCompletion * 100;

        // Calculate overall completion (average of all phases)
        $totalPhases = count($phaseCompletions);
        $overallCompletion = $totalPhases > 0 ? ($sumPhaseCompletions / $totalPhases) / 100 : 0.0;

        return [
            'phases' => $phaseCompletions,
            'overall_completion' => round($overallCompletion, 2),
        ];
    }

    private function calculatePhaseCompletion($tasks, $statusWeightage): float
    {
        $totalTasks = $tasks->count();
        if ($totalTasks === 0) {
            return 0.0;
        }

        $sumTaskCompletion = 0;
        foreach ($tasks as $task) {
            $status = $task->status;
            $taskCompletion = $statusWeightage[$status] ?? 0.0; // Default to 0 if status is invalid
            $sumTaskCompletion += $taskCompletion;
        }

        // Return completion as a decimal (0.0 to 1.0)
        return round($sumTaskCompletion / $totalTasks, 2);
    }

    private function calculateScheduledHandoverCompletion(): float
    {
        $this->rpmJobs;
        $handoverJob = $this->rpmJobs->where('job_category', 'handover')->first();
        $ownerHandoverTask = $handoverJob->rpmTasks->where('item_name', 'Owner Handover')->first();

        return ($ownerHandoverTask->status === 'completed' || $ownerHandoverTask->status === 'in-progress') ? 1 : 0;
    }

    private function calculateSuccessfulHandoverCompletion(): float
    {
        $this->rpmJobs;
        $handoverJob = $this->rpmJobs->where('job_category', 'handover')->first();
        $ownerHandoverTask = $handoverJob->rpmTasks->where('item_name', 'Owner Handover')->first();

        return $ownerHandoverTask->status === 'completed' ? 1 : 0;
    }
}
