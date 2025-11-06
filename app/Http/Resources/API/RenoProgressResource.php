<?php

namespace App\Http\Resources\API;

use App\Models\Finance\Sale;
use App\Models\Operations\RenoProgress;
use App\Models\ResourceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\SaleResource;
use App\Http\Resources\KeyManagementResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DefectInspectionFormResource;

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

        $oldRenoProgress = RenoProgress::where('sale_id', $this->sale_id)->onlyTrashed()->first();

        $data = [
            'id' => $this->id,
            'sale_id' => $this->mainSale->id,
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
            'not-available' => 1.0,
            'not-started' => 0.0,
            'pending' => 0.25,
            'in-progress' => 0.5,
            'completed' => 1.0,
        ];

        // Fetch all jobs with their tasks
        $jobs = $this->rpmJobs;
        $jobCompletions = [];
        $totalJobs = $jobs->count();
        $sumJobCompletions = 0;

        foreach ($jobs as $job) {
            $tasks = $job->rpmTasks;
            $totalTasks = $tasks->count();
            $sumTaskCompletion = 0;

            // Calculate completion for each task
            foreach ($tasks as $task) {
                $status = $task->status;
                $taskCompletion = $statusWeightage[$status] ?? 0.0; // Default to 0 if status is invalid
                $sumTaskCompletion += $taskCompletion;
            }

            // Calculate job completion (avoid division by zero)
            $jobCompletion = $totalTasks > 0 ? ($sumTaskCompletion / $totalTasks) * 100 : 0.0;
            $jobCompletions[] = [
                'job_name' => $job->name,
                'completion_percentage' => round($jobCompletion / 100, 2),
            ];

            $sumJobCompletions += $jobCompletion;
        }

        // Calculate overall completion (avoid division by zero)
        $overallCompletion = $totalJobs > 0 ? ($sumJobCompletions / $totalJobs) : 0.0;

        return [
            'jobs' => $jobCompletions,
            'overall_completion' => round($overallCompletion / 100, 2),
        ];
    }
}
