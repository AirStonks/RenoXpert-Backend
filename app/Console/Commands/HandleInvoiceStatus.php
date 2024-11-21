<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class HandleInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoice status daily and update the status if due_date is passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoices = Invoice::where('status', '!=', 'paid')->get();

        foreach ($invoices as $invoice) {
            if ($invoice->due_date->lte(now())) {
                $invoice->status = 'overdue';
                $invoice->save();
            }
        }

        info("HandleInvoiceStatus Cron Job running at ". now());
    }
}

