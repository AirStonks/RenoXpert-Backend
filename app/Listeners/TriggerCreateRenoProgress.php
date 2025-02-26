<?php

namespace App\Listeners;

use stdClass;
use App\Models\JobTask;
use App\Models\PhaseJob;
use App\Models\Inventory;
use App\Models\RenoProgress;
use App\Models\KeyManagement;
use App\Models\ProgressPhase;
use Illuminate\Support\Facades\Log;
use App\Models\DefectInspectionForm;
use App\Http\Resources\OrderResource;
use App\Events\SaleStatusUpdated; // Updated event name

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

        $this->createRenoProgress($sale);
        // $this->updateOrCreateInventory($sale);
    }

    protected function createRenoProgress($sale)
    {
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

            $p1Phase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'P1',
                'status' => 'not_started',
            ]);

            $p2aPhase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'P2-A',
                'status' => 'not_started',
            ]);

            $p2bPhase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'P2-B',
                'status' => 'not_started',
            ]);

            $iotPhase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'IoT Phase',
                'status' => 'not_started',
            ]);

            $postRenoPhase = ProgressPhase::create([
                'progress_id' => $renoProgress->id,
                'name' => 'Post Reno',
                'status' => 'not_started',
            ]);

            // Add Job part
            $orderQuotations = $sale->order->orderQuotations->sortByDesc('version')->values();
            $latestQuotation = $orderQuotations->first();

            $packages = json_decode($latestQuotation->metadata);

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
                'name' => 'Key Management',
                'is_key_form' => true,
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

            $permitJob = PhaseJob::create([
                'phase_id' => $preRenoPhase->id,
                'name' => 'Reno Permit',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $permitJob->id,
                'name' => 'Permit Application & Submission',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $permitJob->id,
                'name' => 'Permit Deposit Paid by Owner',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $permitJob->id,
                'name' => 'Reno Permit Approval',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $permitJob->id,
                'name' => 'Permit Issued by MO',
                'status' => 'not_started',
            ]);

            // P1, P2-A, P2-B and IoT Phase

            // 1 = others
            // 2 = furniture
            // 3 = wiring
            // 4 = painting
            // 5 = curtain
            // 6 = iot
            // 7 = electrical appliances
            // 8 = loose item
            // 9 = partition

            // P1  = wiring, painting, partition (3, 4, 9)
            // P2A = furniture, curtain (2, 5)
            // P2B = electrical appliances, loose item (7, 8)
            // IoT = iot (6)

            foreach ($packages as $originalPkg) {
                $quantity = $originalPkg->quantity ?? 1;
                $originalName = $originalPkg->name;
                $products = $originalPkg->products;

                if ($quantity > 1) {
                    for ($i = 1; $i <= $quantity; $i++) {
                        $pkg = new stdClass();
                        $pkg->name = "{$originalName} (Room {$i})";
                        $pkg->products = $products;

                        foreach ($pkg->products as $product) {
                            if ($product->pm_category_id === 1) {
                                continue;
                            }

                            // Skip if neither includeSupply nor includeInstall is true
                            if (!$product->pivot->includeSupply && !$product->pivot->includeInstall) {
                                continue;
                            }

                            $jobConfig = [
                                3 => ['phase' => $p1Phase, 'name' => 'House Electrical & Wiring', 'priority' => 1],
                                4 => ['phase' => $p1Phase, 'name' => 'House Painting', 'priority' => 2],
                                9 => ['phase' => $p1Phase, 'name' => 'House Partition', 'priority' => 3],
                                7 => ['phase' => $p2bPhase, 'name' => 'Electrical Appliances', 'priority' => 1],
                                8 => ['phase' => $p2bPhase, 'name' => 'Loose Items', 'priority' => 2],
                                6 => ['phase' => $iotPhase, 'name' => 'Smart IoT Devices', 'priority' => 1]
                            ];

                            if (isset($jobConfig[$product->pm_category_id])) {
                                $config = $jobConfig[$product->pm_category_id];
                                $phase = $config['phase'];
                                $jobName = $config['name'];
                                $priority = $config['priority'];
                            } else {
                                $phase = $p2aPhase;
                                $jobName = $pkg->name;
                                $priority = 1;
                            }

                            $job = PhaseJob::firstOrCreate(
                                [
                                    'name' => $jobName,
                                    'phase_id' => $phase->id
                                ],
                                [
                                    'phase_id' => $phase->id,
                                    'name' => $jobName,
                                    'priority' => $priority,
                                    'status' => 'not_started',
                                ]
                            );

                            JobTask::create([
                                'job_id' => $job->id,
                                'product_id' => $product->id,
                                'qty' => $product->pivot->quantity,
                                'name' => $product->name,
                                'task_weightage' => $product->task_weightage > 0 ? $product->task_weightage : 1,
                                'area' => $pkg->name,
                                'status' => 'not_started',
                            ]);
                        }
                    }
                } else {
                    foreach ($originalPkg->products as $product) {
                        if ($product->pm_category_id === 1) {
                            continue;
                        }

                        // Skip if neither includeSupply nor includeInstall is true
                        if (!$product->pivot->includeSupply && !$product->pivot->includeInstall) {
                            continue;
                        }

                        $jobConfig = [
                            3 => ['phase' => $p1Phase, 'name' => 'House Electrical & Wiring', 'priority' => 1],
                            4 => ['phase' => $p1Phase, 'name' => 'House Painting', 'priority' => 2],
                            9 => ['phase' => $p1Phase, 'name' => 'House Partition', 'priority' => 3],
                            7 => ['phase' => $p2bPhase, 'name' => 'Electrical Appliances', 'priority' => 1],
                            8 => ['phase' => $p2bPhase, 'name' => 'Loose Items', 'priority' => 2],
                            6 => ['phase' => $iotPhase, 'name' => 'Smart IoT Devices', 'priority' => 1]
                        ];

                        if (isset($jobConfig[$product->pm_category_id])) {
                            $config = $jobConfig[$product->pm_category_id];
                            $phase = $config['phase'];
                            $jobName = $config['name'];
                            $priority = $config['priority'];
                        } else {
                            $phase = $p2aPhase;
                            $jobName = $originalPkg->name;
                            $priority = 1;
                        }

                        $job = PhaseJob::firstOrCreate(
                            [
                                'name' => $jobName,
                                'phase_id' => $phase->id
                            ],
                            [
                                'phase_id' => $phase->id,
                                'name' => $jobName,
                                'priority' => $priority,
                                'status' => 'not_started',
                            ]
                        );

                        JobTask::create([
                            'job_id' => $job->id,
                            'product_id' => $product->id,
                            'qty' => $product->pivot->quantity,
                            'name' => $product->name,
                            'task_weightage' => $product->task_weightage > 0 ? $product->task_weightage : 1,
                            'area' => $originalPkg->name,
                            'status' => 'not_started',
                        ]);
                    }
                }
            }

            // Post Reno
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

            JobTask::create([
                'job_id' => $postRenoJob->id,
                'name' => 'Lock Transfer',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $postRenoJob->id,
                'name' => 'Meter Commissioning and Testing',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $postRenoJob->id,
                'name' => 'WiFi Pairing',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $postRenoJob->id,
                'name' => 'Account and Password',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $postRenoJob->id,
                'name' => 'Deposit Refund Monitoring',
                'status' => 'not_started',
            ]);

            JobTask::create([
                'job_id' => $postRenoJob->id,
                'name' => 'RPM Handover',
                'status' => 'not_started',
            ]);

            // Create DI Form
            $metadata = [
                'yard' => [
                    'q1' => ['value' => '', 'remark' => null],
                    'q2' => ['value' => '', 'remark' => null],
                    'q3' => ['value' => '', 'remark' => null],
                    'q4' => ['value' => '', 'remark' => null],
                    'q5' => ['value' => '', 'remark' => null],
                    'q6' => ['value' => '', 'remark' => null],
                ],
                'foyer' => [
                    'q1' => ['value' => '', 'remark' => null],
                    'q2' => ['value' => '', 'remark' => null],
                    'q3' => ['value' => '', 'remark' => null],
                    'q4' => ['value' => '', 'remark' => null],
                ],
                'living' => [
                    'q1' => ['value' => '', 'remark' => null],
                    'q2' => ['value' => '', 'remark' => null],
                    'q3' => ['value' => '', 'remark' => null],
                    'q4' => ['value' => '', 'remark' => null],
                    'q5' => ['value' => '', 'remark' => null],
                    'q6' => ['value' => '', 'remark' => null],
                    'q7' => ['value' => '', 'remark' => null],
                    'q8' => ['value' => '', 'remark' => null],
                    'q9' => ['value' => '', 'remark' => null],
                ],
                'balcony' => [
                    'q1' => ['value' => '', 'remark' => null],
                    'q2' => ['value' => '', 'remark' => null],
                    'q3' => ['value' => '', 'remark' => null],
                    'q4' => ['value' => '', 'remark' => null],
                ],
                'hallway' => [
                    'q1' => ['value' => '', 'remark' => null],
                    'q2' => ['value' => '', 'remark' => null],
                    'q3' => ['value' => '', 'remark' => null],
                    'q4' => ['value' => '', 'remark' => null],
                ],
                'kitchen' => [
                    'q1' => ['value' => '', 'remark' => null],
                    'q2' => ['value' => '', 'remark' => null],
                    'q3' => ['value' => '', 'remark' => null],
                    'q4' => ['value' => '', 'remark' => null],
                    'q5' => ['value' => '', 'remark' => null],
                    'q6' => ['value' => '', 'remark' => null],
                    'q7' => ['value' => '', 'remark' => null],
                    'q8' => ['value' => '', 'remark' => null],
                ],
                'bedrooms' => [],
                'bathrooms' => [],
            ];

            // Generate bedroom entries dynamically
            $bedroomTemplate = [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
                'q7' => ['value' => '', 'remark' => null],
                'q8' => ['value' => '', 'remark' => null],
                'q9' => ['value' => '', 'remark' => null],
            ];

            for ($i = 1; $i <= $sale->order->bedroom_count; $i++) {
                $metadata['bedrooms']["bedroom{$i}"] = $bedroomTemplate;
            }

            // Generate bathroom entries dynamically
            $bathroomTemplate = [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
                'q7' => ['value' => '', 'remark' => null],
                'q8' => ['value' => '', 'remark' => null],
                'q9' => ['value' => '', 'remark' => null],
            ];

            for ($i = 1; $i <= $sale->order->bathroom_count; $i++) {
                $metadata['bathrooms']["bathroom{$i}"] = $bathroomTemplate;
            }

            $form = DefectInspectionForm::create([
                'reno_progress_id' => $renoProgress->id,
                'property_name' => $sale->order->property_id,
                'owner_email' => $sale->order->user->email,
                'other_property_name' => null,
                'block' => $sale->order->block,
                'level' => $sale->order->floor,
                'unit' => $sale->order->unit_no,
                'status' => 'not_submitted',
                'bedroom_count' => $sale->order->bedroom_count,
                'bathroom_count' => $sale->order->bathroom_count,
                'metadata' => json_encode($metadata),
            ]);

            // Create KeyManagement
            $keyManagement = KeyManagement::create([
                'reno_progress_id' => $renoProgress->id,
                'metadata' => json_encode([
                    ['name' => 'ori_acc_card', 'remark' => '', 'value' => []],
                    ['name' => 'dup_acc_card', 'remark' => '', 'value' => []],
                    ['name' => 'car_acc_card', 'remark' => '', 'value' => []],
                    ['name' => 'main_door_key', 'remark' => '', 'value' => []],
                    ['name' => 'room_door_key', 'remark' => '', 'value' => []],
                    ['name' => 'yard_door_key', 'remark' => '', 'value' => []],
                    ['name' => 'mailbox_key', 'remark' => '', 'value' => []],
                    ['name' => 'ac_ledge_key', 'remark' => '', 'value' => []],
                    ['name' => 'ac_remote', 'remark' => '', 'value' => []],
                    ['name' => 'others', 'remark' => '', 'value' => []],
                ]),
            ]);
        } catch (\Exception $e) {
            Log::error('Error triggering RenoProgress creation for sale ID ' . $sale->id . ': ' . $e->getMessage());
            // Optionally rethrow or handle the exception as needed
            throw $e;
        }
    }

    // protected function updateOrCreateInventory($sale)
    // {

    //     try {
    //         $order = $sale->order;
    //         $order->orderQuotations->load('quotation');

    //         $orderQuotations = $order->orderQuotations->sortByDesc('version')->values();
    //         $latestQuotation = $orderQuotations->first();

    //         // Extract the latest_quotation from the array
    //         $packages = json_decode($latestQuotation->metadata);

    //         foreach ($packages as $pkg) {
    //             foreach ($pkg->products as $product) {
    //                 if ($product->pm_category_id !== 1) {
    //                     $inventory = Inventory::firstOrCreate(
    //                         ['product_id' => $product->id],
    //                         [
    //                             'total_required_stock' => $product->pivot->quantity,
    //                             'required_stock' => $product->pivot->quantity,
    //                         ]
    //                     );

    //                     // Only increment quantities if the inventory already existed
    //                     if ($inventory->wasRecentlyCreated === false) {
    //                         $inventory->increment('total_required_stock', $product->pivot->quantity);
    //                         $inventory->increment('required_stock', $product->pivot->quantity);
    //                     }
    //                 }
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Error triggering Inventory update or creation for sale ID ' . $sale->id . ': ' .
    //             $e->getMessage());
    //         // Optionally rethrow or handle the exception as needed
    //         throw $e;
    //     }
    // }
}
