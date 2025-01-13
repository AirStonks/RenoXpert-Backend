<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Models\Inventory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TriggerCreateUpdateInventory
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
    public function handle(SaleCreated $event): void
    {
        $sale = $event->sale;
        $this->updateOrCreateInventory($sale);
    }

    protected function updateOrCreateInventory($sale)
    {

        try {
            $order = $sale->order;
            $order->orderQuotations->load('quotation');

            $orderQuotations = $order->orderQuotations->sortByDesc('version')->values();
            $latestQuotation = $orderQuotations->first();

            // Extract the latest_quotation from the array
            $packages = json_decode($latestQuotation->metadata);

            foreach ($packages as $pkg) {
                foreach ($pkg->products as $product) {
                    if ($product->pm_category_id !== 1) {
                        // if ($product->pivot->includeSupply == true) {
                            $inventory = Inventory::firstOrCreate(
                                ['product_id' => $product->id],
                                [
                                    'total_required_stock' => $product->pivot->quantity,
                                    'required_stock' => $product->pivot->quantity,
                                ]
                            );

                            // Only increment quantities if the inventory already existed
                            if ($inventory->wasRecentlyCreated === false) {
                                $inventory->increment('total_required_stock', $product->pivot->quantity);
                                $inventory->increment('required_stock', $product->pivot->quantity);
                            }
                        // }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error triggering Inventory update or creation for sale ID ' . $sale->id . ': ' .
                $e->getMessage());
            // Optionally rethrow or handle the exception as needed
            throw $e;
        }
    }
}
