<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseOrderResource;
use App\Models\Inventory;
use App\Models\POItem;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve the size parameter from the request with a default value of 5
        $size = $request->input('size', 5);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Build the query to retrieve property
        $query = PurchaseOrder::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                // Search across individual fields
                $query->where('po_no', 'like', '%' . $search . '%')
                    ->orWhere('product_name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results
        $form = $query->paginate($size);


        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $form->currentPage(),  // Current page number
            "pageCount" => $form->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $form->total(),  // Total number of items
            "data" => PurchaseOrderResource::collection($form->items()) // Transformed property data
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            $lastPOId = PurchaseOrder::max('id') ?? 0;

            $input['po_no'] = 'RPO-' . now()->format('y') . str_pad($lastPOId + 1, 5, '0', STR_PAD_LEFT);

            // Create PO
            $newPo = PurchaseOrder::create($input);

            // Create POItems
            foreach ($request->items as $item) {
                $item['po_id'] = $newPo->id;
                $newPo->poItems()->create($item);
            }

            // If POItem create, check the SKU/id is match with the inventory
            foreach ($newPo->poItems as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)->first();

                // If not found, create new inventory
                if (!$inventory) {
                    if ($item->supply) {
                        Inventory::create([
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'sku' => $item->sku,
                            'coming_stock' => $item->qty,
                            // 'category' => $poItem->category,
                        ]);
                    }
                } else {
                    if ($item->supply) {
                        // Else update the inventory stock 
                        $inventory->coming_stock = $inventory->coming_stock + $item->qty;

                        $inventory->save();
                    }
                }
            }

            return $this->sendResponse(new PurchaseOrderResource($newPo), 'Property added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $po = PurchaseOrder::find($id);

        if (is_null($po)) {
            return $this->sendError('Purchase Order not found.');
        }

        return $this->sendResponse(new PurchaseOrderResource($po, true), 'Purchase Order retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
