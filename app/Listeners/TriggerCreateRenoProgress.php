<?php

namespace App\Listeners;

use App\Events\SaleStatusUpdated; // Updated event name
use App\Models\JobTask;
use App\Models\PhaseJob;
use App\Models\ProgressPhase;
use App\Models\RenoProgress;
use Illuminate\Support\Facades\Log;

class TriggerCreateRenoProgress
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaleStatusUpdated $event): void
    {
        $sale = $event->sale;

        try {
            // Create a new RenoProgress record with 'in_progress' status
            $renoProgress = RenoProgress::create([
                'sale_id' => $sale->id,
                'status' => 'in_progress',
            ]);

            $preRenoPhase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'Pre Reno',
                'status' => 'in_progress',
            ]);

            $renoPhase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'Reno',
                'status' => 'not_started',
            ]);

            $postRenoPhase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'Post Reno',
                'status' => 'not_started',
            ]);

            // Add Job part
            $orderQuotation = $sale->order->orderQuotations->first();

            $packages = json_decode($orderQuotation->metadata);

            // Hardcode for Pre Reno Jobs and Tasks
            $vpJob = PhaseJob::create([
                'phase_id' => $preRenoPhase->id,
                'name' => 'VP Status',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $vpJob->id,
                'name' => 'VP',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $vpJob->id,
                'name' => 'Key Handover',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $vpJob->id,
                'name' => 'TNB',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $vpJob->id,
                'name' => 'Water Supply',
                'status' => 'not_started',
            ]);

            $defectJob = PhaseJob::create([
                'phase_id' => $preRenoPhase->id,
                'name' => 'Defect Status',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $defectJob->id,
                'name' => 'Defect Inspection [Submit DIR]',
                'is_defect_form' => true,
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $defectJob->id,
                'name' => 'Defect Submission to Developer',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $defectJob->id,
                'name' => 'Defect Rectification by Developer',
                'status' => 'not_started',
            ]);

            foreach ($packages as $pkg) {
                foreach ($pkg->products as $product) {
                    if ($product->pm_category_id === 1) {
                        continue; // Skip this iteration and move to the next product
                    }

                    if ($product->pm_category_id === 3) {
                        // FindOrCreate Wiring Job
                        $job = PhaseJob::firstOrCreate(
                            ['name' => 'House Electrical & Wiring'],
                            [
                                'phase_id' => $renoPhase->id,
                                'name' => 'House Electrical & Wiring',
                                'priority' => 3,
                                'status' => 'not_started',
                            ]
                        );

                        // Create wiring Task
                        $task = JobTask::create([
                            'job_id' => $job->id,
                            'name' => $product->name . ' (' . $pkg->name . ')',
                            'status' => 'not_started',
                        ]);
                    } elseif ($product->pm_category_id === 4) {
                        // FindOrCreate Wiring Job
                        $job = PhaseJob::firstOrCreate(
                            ['name' => 'House Painting'],
                            [
                                'phase_id' => $renoPhase->id,
                                'name' => 'House Painting',
                                'priority' => 2,
                                'status' => 'not_started',
                            ]
                        );

                        // Create painting Task
                        $task = JobTask::create([
                            'job_id' => $job->id,
                            'name' => $product->name . ' (' . $pkg->name . ')',
                            'status' => 'not_started',
                        ]);
                    } else {
                        // FindOrCreate Package Job
                        $job = PhaseJob::firstOrCreate(
                            ['name' => $pkg->name],
                            [
                                'phase_id' => $renoPhase->id,
                                'name' => $pkg->name,
                                'status' => 'not_started',
                            ]
                        );

                        // Create Package Task
                        $task = JobTask::create([
                            'job_id' => $job->id,
                            'name' => $product->name,
                            'status' => 'not_started',
                        ]);
                    }
                }
            }

            $postRenoJob = PhaseJob::create([
                'phase_id' => $postRenoPhase->id,
                'name' => 'Post Reno Phrase',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $postRenoJob->id,
                'name' => 'QC [QC Form]',
                'is_qc_form' => true,
                'status' => 'not_started',
            ]);

        } catch (\Exception $e) {
            Log::error('Error triggering RenoProgress creation for sale ID ' . $sale->id . ': ' . $e->getMessage());
            // Optionally rethrow or handle the exception as needed
            throw $e;
        }
    }
}
