<?php

namespace App\Console\Commands;

use App\Models\RenoProgress;
use App\Models\ProjectStatusHistory;
use App\Models\Sale;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateWeeklyProjectStatusHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project-status:generate-weekly {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate weekly project status history snapshots for all active renovation projects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting weekly project status history generation...');

        // Get the date to generate snapshots for (default to current date)
        $snapshotDate = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $snapshotYear = $snapshotDate->year;
        $snapshotWeek = $snapshotDate->week;

        $this->info("Generating snapshots for week {$snapshotWeek} of {$snapshotYear}");

        try {
            // Get all active renovation progress records with their related data
            $renoProgressRecords = RenoProgress::with([
                'mainSale.order.property',
                'sales.order.property'
            ])
                ->whereNotNull('status')
                ->where('status', '!=', 'completed')
                ->get();

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($renoProgressRecords as $renoProgress) {
                // Get property information from the main sale
                $property = null;
                $unit = null;

                if ($renoProgress->mainSale && $renoProgress->mainSale->order) {
                    $property = $renoProgress->mainSale->order->property;
                    $unit = $this->buildUnitString($renoProgress->mainSale->order);
                }

                // If no main sale, try to get from any sale
                if (!$property && $renoProgress->sales->isNotEmpty()) {
                    $sale = $renoProgress->sales->first();
                    if ($sale && $sale->order) {
                        $property = $sale->order->property;
                        $unit = $this->buildUnitString($sale->order);
                    }
                }

                // Calculate payment percentage based on status
                $paymentPercentage = $this->calculatePaymentPercentage($renoProgress->status);

                // Check if snapshot already exists for this week
                $existingSnapshot = ProjectStatusHistory::where('reno_progress_id', $renoProgress->id)
                    ->where('snapshot_year', $snapshotYear)
                    ->where('snapshot_week', $snapshotWeek)
                    ->where('snapshot_type', 'weekly')
                    ->first();

                if ($existingSnapshot) {
                    $skippedCount++;
                    $this->line("Skipped: Snapshot already exists for RenoProgress ID {$renoProgress->id}");
                    continue;
                }

                // Create the snapshot
                ProjectStatusHistory::create([
                    'reno_progress_id' => $renoProgress->id,
                    'property_id' => $property ? $property->id : null,
                    'unit' => $unit,
                    'status' => $renoProgress->status,
                    'payment_percentage' => ($renoProgress->sales->flatMap(function ($sale) {
                        return $sale->invoices->where('status', 'paid')->map(function ($invoice) use ($sale) {
                            return $sale->total_amount * ($invoice->percentage / 100);
                        });
                    })->sum() / $renoProgress->sales->sum('total_amount')) * 100,
                    'snapshot_year' => $snapshotYear,
                    'snapshot_week' => $snapshotWeek,
                    'snapshot_date' => $snapshotDate->format('Y-m-d'),
                    'snapshot_type' => 'weekly',
                ]);

                $createdCount++;
                $this->line("Created snapshot for RenoProgress ID {$renoProgress->id}");
            }

            $this->info("Weekly project status history generation completed!");
            $this->info("Created: {$createdCount} snapshots");
            $this->info("Skipped: {$skippedCount} snapshots (already exist)");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error generating weekly project status history: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Build unit string from order data
     */
    private function buildUnitString($order): ?string
    {
        $unitParts = [];

        if ($order->block) {
            $unitParts[] = "{$order->block}";
        }

        if ($order->floor) {
            $unitParts[] = "{$order->floor}";
        }

        if ($order->unit_no) {
            $unitParts[] = "{$order->unit_no}";
        }

        return !empty($unitParts) ? implode('-', $unitParts) : null;
    }

    /**
     * Calculate payment percentage based on status
     */
    private function calculatePaymentPercentage(string $status): float
    {
        $statusPaymentMap = [
            'pending-vp' => 0.0,
            'vp-approved' => 10.0,
            'design-approved' => 20.0,
            'contract-signed' => 30.0,
            'work-started' => 40.0,
            'p1-in-progress' => 50.0,
            'p1-completed' => 60.0,
            'p2-in-progress' => 70.0,
            'p2-completed' => 80.0,
            'qc-in-progress' => 85.0,
            'qc-completed' => 90.0,
            'pc-in-progress' => 95.0,
            'pc-completed' => 98.0,
            'completed' => 100.0,
        ];

        return $statusPaymentMap[$status] ?? 0.0;
    }
}
