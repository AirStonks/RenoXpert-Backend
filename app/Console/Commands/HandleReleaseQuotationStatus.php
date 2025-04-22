<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class HandleReleaseQuotationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:release-status-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check order released date daily and update the order status if released_at is 7 days ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status', '=', 'released')->get();

        foreach ($orders as $order) {
            // Convert string to Carbon instance
            $releasedDate = Carbon::parse($order->released_at);

            // Check if released_at is 14 days ago
            if ($releasedDate->addDays(7)->endOfDay()->lte(now())) {
                $order->status = 'voided';
                $order->save();
            }
        }

        info("HandleReleaseQuotationStatus Cron Job running at " . now());
    }
}
