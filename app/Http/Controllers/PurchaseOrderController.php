<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\PurchaseOrderResourceHead;
use App\Models\Inventory;
use App\Models\POItem;
use App\Models\POPackage;
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
                $query->where('po_no', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results
        $po = $query->paginate($size);


        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $po->currentPage(),  // Current page number
            "pageCount" => $po->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $po->total(),  // Total number of items
            "data" => $request->input('head') === 'true' ? PurchaseOrderResourceHead::collection($po) : PurchaseOrderResource::collection($po)
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

            // Calculate total amount from po_packages
            $totalAmount = $this->calculateTotalAmount($input['po_packages'] ?? []);
            $input['total_amount'] = $totalAmount;

            // Create PO
            $newPo = PurchaseOrder::create([
                'po_no' => $input['po_no'],
                'sale_id' => $input['sale_id'],
                'vendor_id' => $input['vendor_id'],
                'total_amount' => $input['total_amount'],
                // Add other fields like 'order_status', 'payment_status' if provided in $input
            ]);

            // Create POPackages
            if (isset($input['po_packages']) && is_array($input['po_packages'])) {
                foreach ($input['po_packages'] as $packageData) {
                    $newPackage = POPackage::create([
                        'po_id' => $newPo->id,
                        'name' => $packageData['name'],
                        'description' => $packageData['description'] ?? null,
                        'description_internal' => $packageData['description_internal'] ?? null,
                        'category' => $packageData['category'],
                        'quantity' => $packageData['quantity'],
                        'total_price' => $packageData['total_price'],
                    ]);

                    // Create POItems for this package
                    if (isset($packageData['po_items']) && is_array($packageData['po_items'])) {
                        foreach ($packageData['po_items'] as $itemData) {
                            POItem::create([
                                'po_package_id' => $newPackage->id,
                                'product_id' => $itemData['product_id'],
                                'product_name' => $itemData['product_name'],
                                'qty' => $itemData['qty'],
                                'uom' => $itemData['uom'],
                                'supply' => $itemData['supply'],
                                'install' => $itemData['install'],
                                'supply_price' => $itemData['supply_price'],
                                'install_price' => $itemData['install_price'],
                                'unit_price' => $itemData['unit_price'],
                                'total_price' => $itemData['total_price'],
                                // Add other fields like 'status' if provided
                            ]);
                        }
                    }
                }
            }

            // // If POItem create, check the SKU/id is match with the inventory
            // foreach ($newPo->poItems as $item) {
            //     $inventory = Inventory::where('product_id', $item->product_id)->first();

            //     // If not found, create new inventory
            //     if (!$inventory) {
            //         if ($item->supply) {
            //             Inventory::create([
            //                 'product_id' => $item->product_id,
            //                 'product_name' => $item->product_name,
            //                 'sku' => $item->sku,
            //                 'coming_stock' => $item->qty,
            //                 // 'category' => $poItem->category,
            //             ]);
            //         }
            //     } else {
            //         if ($item->supply) {
            //             // Else update the inventory stock 
            //             $inventory->coming_stock = $inventory->coming_stock + $item->qty;

            //             $inventory->save();
            //         }
            //     }
            // }

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

    /**
     * Calculate total amount based on PO packages and items
     * @param array $poPackages
     * @return float
     */
    private function calculateTotalAmount(array $poPackages): float
    {
        return array_reduce($poPackages, function ($total, $packageItem) {
            $packageTotal = array_reduce($packageItem['po_items'] ?? [], function ($packageTotal, $product) {
                $productTotal = $product['qty'] * (
                    ($product['supply'] ? $product['supply_price'] : 0) +
                    ($product['install'] ? $product['install_price'] : 0)
                );
                return $packageTotal + $productTotal;
            }, 0);

            return $total + ($packageTotal * ($packageItem['quantity'] ?? 1));
        }, 0);
    }
}
