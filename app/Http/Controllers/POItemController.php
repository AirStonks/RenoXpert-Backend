<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\POItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class POItemController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(POItem $pOItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, POItem $pOItem)
    {
        //
    }

    public function markAsDelivered($id)
    {
        $poItem = POItem::find($id);

        $poItem->status = 'delivered';

        $poItem->delivered_date = Carbon::now();

        $poItem->save();

        // Update Inventory
        $inventory = Inventory::where('product_id', $poItem->product_id)->first();

        $inventory->current_stock += $poItem->qty;
        $inventory->coming_stock -= $poItem->qty;

        $inventory->save();

        return $this->sendResponse($poItem, 'PO Item mark as delivered successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(POItem $pOItem)
    {
        //
    }
}
