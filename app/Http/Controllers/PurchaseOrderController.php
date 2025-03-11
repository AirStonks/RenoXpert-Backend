<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\PurchaseOrderResourceHead;
use App\Models\Inventory;
use App\Models\POItem;
use App\Models\POPackage;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            // Step 1: Validate the input
            $validator = Validator::make($input, [
                'sale_id' => 'nullable|exists:sales,id', // Changed to nullable
                'vendor_id' => 'required|exists:users,id',
                'po_packages' => 'required|array',
                'po_packages.*.package_id' => 'required|integer',
                'po_packages.*.name' => 'required|string',
                'po_packages.*.quantity' => 'required|integer|min:1',
                'po_packages.*.total_price' => 'required|numeric|min:0',
                'po_packages.*.po_items' => 'sometimes|array',
                'po_packages.*.po_items.*.product_id' => 'required|integer',
                'po_packages.*.po_items.*.product_name' => 'required|string',
                'po_packages.*.po_items.*.product_desc' => 'nullable|string',
                'po_packages.*.po_items.*.qty' => 'required|integer|min:1',
                'po_packages.*.po_items.*.supply' => 'required|boolean',
                'po_packages.*.po_items.*.install' => 'required|boolean',
                'po_packages.*.po_items.*.supply_price' => 'required|numeric|min:0',
                'po_packages.*.po_items.*.install_price' => 'required|numeric|min:0',
                'po_packages.*.po_items.*.unit_price' => 'required|numeric|min:0',
                'po_packages.*.po_items.*.total_price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Step 2: Generate PO number
            $lastPOId = PurchaseOrder::max('id') ?? 0;
            $input['po_no'] = 'RPO-' . now()->format('y') . str_pad($lastPOId + 1, 5, '0', STR_PAD_LEFT);

            // Step 3: Calculate total amount
            $totalAmount = $this->calculateTotalAmount($input['po_packages'] ?? []);
            $input['total_amount'] = $totalAmount;

            // Step 4: Create everything in a transaction
            $newPo = DB::transaction(function () use ($input) {
                // Create PO
                $newPo = PurchaseOrder::create([
                    'po_no' => $input['po_no'],
                    'sale_id' => $input['sale_id'] ?? null, // Allow null
                    'vendor_id' => $input['vendor_id'],
                    'total_amount' => $input['total_amount'],
                    'order_status' => $input['order_status'] ?? 'unreleased',
                    'payment_status' => $input['payment_status'] ?? 'unpaid',
                ]);

                if (!$newPo) {
                    throw new \Exception('Failed to create Purchase Order');
                }

                // Create POPackages with sequence
                if (isset($input['po_packages']) && is_array($input['po_packages'])) {
                    foreach ($input['po_packages'] as $index => $packageData) {
                        $sequence = $index + 1; // Sequence starts at 1

                        $newPackage = POPackage::create([
                            'po_id' => $newPo->id,
                            'package_id' => (int) $packageData['package_id'],
                            'name' => $packageData['name'],
                            'description' => $packageData['description'] ?? null,
                            'description_internal' => $packageData['description_internal'] ?? null,
                            'category' => $packageData['category'] ?? null,
                            'quantity' => $packageData['quantity'],
                            'total_price' => $packageData['total_price'],
                            'sequence' => $sequence,
                        ]);

                        if (!$newPackage) {
                            throw new \Exception('Failed to create PO Package for package_id: ' . $packageData['package_id']);
                        }

                        // Create POItems for this package with sequence
                        if (isset($packageData['po_items']) && is_array($packageData['po_items'])) {
                            foreach ($packageData['po_items'] as $itemIndex => $itemData) {
                                $itemSequence = $itemIndex + 1; // Sequence starts at 1

                                $newItem = POItem::create([
                                    'po_package_id' => $newPackage->id,
                                    'product_id' => $itemData['product_id'],
                                    'product_name' => $itemData['product_name'],
                                    'product_desc' => $itemData['product_desc'],
                                    'qty' => $itemData['qty'],
                                    'uom' => $itemData['uom'] ?? null,
                                    'supply' => $itemData['supply'],
                                    'install' => $itemData['install'],
                                    'supply_price' => $itemData['supply_price'],
                                    'install_price' => $itemData['install_price'],
                                    'unit_price' => $itemData['unit_price'],
                                    'total_price' => $itemData['total_price'],
                                    'sequence' => $itemSequence,
                                    'status' => $itemData['status'] ?? 'pending',
                                ]);

                                if (!$newItem) {
                                    throw new \Exception('Failed to create PO Item for product_id: ' . $itemData['product_id']);
                                }
                            }
                        }
                    }
                }

                return $newPo;
            });

            // Optional: Inventory logic (uncomment if needed)
            /*
        foreach ($newPo->poPackages as $package) {
            foreach ($package->poItems as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)->first();
                if (!$inventory) {
                    if ($item->supply) {
                        Inventory::create([
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'sku' => $item->sku,
                            'coming_stock' => $item->qty,
                        ]);
                    }
                } else {
                    if ($item->supply) {
                        $inventory->coming_stock = $inventory->coming_stock + $item->qty;
                        $inventory->save();
                    }
                }
            }
        }
        */

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
        $input = $request->all();

        // Validate the request
        $validator = Validator::make($input, [
            'po_packages' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Perform updates within a transaction
        DB::transaction(function () use ($request, $purchaseOrder, $input) {
            // Step 1: Update the Purchase Order's main attributes
            $purchaseOrder->update($request->except('po_packages'));

            // Step 2: Get the input packages
            $inputPackages = $input['po_packages'] ?? [];

            // Step 3: Collect IDs of existing packages from the input
            $inputPackageIds = collect($inputPackages)
                ->filter(fn($package) => isset($package['id']) && $package['id'])
                ->pluck('id')
                ->all();

            // Track new package IDs
            $newPackageIds = [];

            // Step 4: Process each input package with sequence
            foreach ($inputPackages as $index => $packageData) {
                $sequence = $index + 1;

                if (isset($packageData['id']) && $packageData['id']) {
                    // Existing package: update it
                    $package = POPackage::find($packageData['id']);
                    if ($package) {
                        $package->update(array_merge(
                            $packageData,
                            ['sequence' => $sequence]
                        ));
                    } else {
                        continue;
                    }
                } else {
                    // New package: create it
                    $package = POPackage::create(
                        array_merge($packageData, [
                            'po_id' => $purchaseOrder->id,
                            'sequence' => $sequence,
                        ])
                    );
                    $newPackageIds[] = $package->id; // Add new package ID
                }

                // Step 5: Handle items for this package with sequence
                $inputItems = $packageData['po_items'] ?? [];
                $inputItemIds = collect($inputItems)
                    ->filter(fn($item) => isset($item['id']) && $item['id'])
                    ->pluck('id')
                    ->all();

                $newItemIds = []; // Track new item IDs

                foreach ($inputItems as $itemIndex => $itemData) {
                    $itemSequence = $itemIndex + 1;

                    if (isset($itemData['id']) && $itemData['id']) {
                        // Existing item: update it
                        $item = POItem::find($itemData['id']);
                        if ($item) {
                            $item->update(array_merge(
                                $itemData,
                                ['sequence' => $itemSequence]
                            ));
                        }
                    } else {
                        // New item: create it
                        $item = POItem::create(
                            array_merge($itemData, [
                                'po_package_id' => $package->id,
                                'sequence' => $itemSequence,
                            ])
                        );
                        $newItemIds[] = $item->id; // Add new item ID
                    }
                }

                // Step 6: Remove items not in the input (soft delete)
                $allItemIds = array_merge($inputItemIds, $newItemIds); // Include new IDs
                POItem::where('po_package_id', $package->id)
                    ->whereNotIn('id', $allItemIds)
                    ->delete();
            }

            // Step 7: Remove packages not in the input (soft delete)
            $allPackageIds = array_merge($inputPackageIds, $newPackageIds);
            POPackage::where('po_id', $purchaseOrder->id)
                ->whereNotIn('id', $allPackageIds)
                ->delete();
        });

        // Return success response with the updated Purchase Order
        return $this->sendResponse(
            $purchaseOrder->fresh()->load('poPackages.poItems'),
            'Purchase Order updated successfully.'
        );
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
