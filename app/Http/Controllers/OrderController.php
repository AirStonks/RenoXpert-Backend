<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OrderQuotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\OrderResourceHead;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OwnerOrderResource;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve the size parameter from the request with a default value of 10
        $size = $request->input('size', 10);
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'id'); // Default to 'id'
        $sortOrder = $request->input('sortOrder', 'desc'); // Default to 'desc'

        // Build the query to retrieve orders
        $query = Order::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                // Search across individual fields in the orders table
                $query->where('order_no', 'like', '%' . $search . '%')
                    ->orWhere('internal_remark', 'like', '%' . $search . '%')
                    // Search in the related user's fields
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name_first', 'like', '%' . $search . '%')
                            ->orWhere('name_last', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(name_first, ' ', name_last) LIKE ?", ['%' . $search . '%']);
                    })
                    // Search in the related property's fields
                    ->orWhereHas('property', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply filters if provided in the request
        $filters = $request->input('filters');

        if ($filters && !empty($filters)) {
            foreach ($filters as $filter) {
                if ($filter['field'] === 'status') {
                    if ($filter['value'] !== 'all') {
                        $query->where('status', $filter['value']);
                    }
                }
                if ($filter['field'] === 'property_id') {
                    if ($filter['value']) {
                        $query->where('property_id', $filter['value']);
                    }
                }
            }
        }

        // Apply sorting
        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('id', 'desc'); // Fallback to 'id' if sortField is empty
        }

        // Paginate results
        $orders = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $orders->currentPage(),  // Current page number
            "pageCount" => $orders->lastPage(), // Total number of pages
            "sortField" => $sortField, // Sorting field
            "sortOrder" => $sortOrder, // Sorting order
            "totalCount" => $orders->total(),   // Total number of items
            "data" => $request->input('head') === 'true' ? OrderResourceHead::collection($orders) : OrderResource::collection($orders) // Transformed order data
        ];

        return response()->json($response, 200);
    }

    public function getOwnerOrders()
    {
        $user = Auth::user();

        // Assuming you have a relationship set up in your Order model to access the contact
        $orders = Order::where('status', '!=', 'unreleased')
            ->whereHas('user', function ($query) use ($user) {
                $query->where('phone_no', $user->phone_no);
            })
            ->get();

        return $this->sendResponse(OwnerOrderResource::collection($orders), 'Order retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            // Validate the input
            $validator = Validator::make($input, [
                'user_id' => 'nullable|numeric|max:255',
                'form_id' => 'nullable',
                'property_id' => 'nullable|numeric|min:0',
                'quotation_id' => 'nullable|numeric|min:0',
                'unit_type' => 'nullable|string|max:255',
                'block' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'unit_no' => 'nullable|string|max:255',
                // 'bedroom_count' => 'nullable',
                'single_bedroom_count' => 'nullable',
                'queen_bedroom_count' => 'nullable',
                'studio_count' => 'nullable',
                'bathroom_count' => 'nullable',
                'include_partition' => 'nullable|boolean',
                'is_progressive_payment' => 'nullable|boolean',
                'is_be_powered' => 'nullable|boolean',
                'installment_method' => 'nullable|string|max:255',
                'installment_amount' => 'nullable|numeric|min:0',
                'be_powered_base_price' => 'nullable|numeric|min:0',
                'final_amount' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:0',
                'internal_remark' => 'nullable|string|min:0',
                'completion_day' => 'nullable|numeric|min:0',
                'tenure' => 'nullable|numeric|min:0',
                'metadata' => 'nullable', // Added validation for metadata
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $input['bedroom_count'] = $input['single_bedroom_count'] + $input['queen_bedroom_count'] + $input['studio_count'];
            $isDraftMode = $input['user_id'] == null;

            // Determine last order number based on draft or regular orders
            if ($isDraftMode) {
                // Get the last draft order's number - FIXED: Include ALL records (even soft deleted)
                $lastDraftOrder = Order::withTrashed() // Include soft deleted records
                    ->where('order_no', 'like', 'DRAFT-%')
                    ->orderBy('id', 'desc')
                    ->first();
                $lastOrderNumber = $lastDraftOrder ? ((int)substr($lastDraftOrder->order_no, -5)) : 0;

                // Generate new draft order number with collision check
                do {
                    $lastOrderNumber++;
                    $newOrderNumber = 'DRAFT-' . now()->format('y') . str_pad($lastOrderNumber, 5, '0', STR_PAD_LEFT);

                    $exists = Order::withTrashed() // Include soft deleted records in collision check
                        ->where('order_no', $newOrderNumber)
                        ->exists();
                } while ($exists);

                $input['order_no'] = $newOrderNumber;
            } else {
                // Get the highest existing order number with prefix 'QUO-' - FIXED: Include ALL records
                $lastConfirmedOrder = Order::withTrashed() // Include soft deleted records
                    ->where('order_no', 'like', 'QUO-%')
                    ->orderBy('order_no', 'desc')
                    ->first();

                $lastOrderNumber = $lastConfirmedOrder ? ((int)substr($lastConfirmedOrder->order_no, -5)) : 0;

                // Generate a unique order number with collision check
                do {
                    $lastOrderNumber++;
                    $newOrderNumber = 'QUO-' . now()->format('y') . str_pad($lastOrderNumber, 5, '0', STR_PAD_LEFT);

                    $exists = Order::withTrashed() // Include soft deleted records in collision check
                        ->where('order_no', $newOrderNumber)
                        ->exists();
                } while ($exists);

                $input['order_no'] = $newOrderNumber;
            }

            $bonusValue = isset($input['bonus']['value']) && !empty($input['bonus']['value']) && (float)$input['bonus']['value'] != 0
                ? (float)$input['bonus']['value']
                : 0;

            // Check if bonus exists and is an array
            if (isset($input['bonus']) && is_array($input['bonus'])) {
                // Check if 'value' in 'bonus' is '', null, or 0
                if (empty($input['bonus']['value']) || $input['bonus']['value'] == 0) {
                    // Set bonus as null if value is empty, null, or 0
                    $input['bonus'] = null;
                } else {
                    // Otherwise, encode the bonus as JSON
                    $input['bonus'] = json_encode($input['bonus']);
                }
            } else {
                // If no bonus exists or it's not an array, set it as null
                $input['bonus'] = null;
            }

            if ($input['user_id'] == null) {
                $input['status'] = 'draft';
            }

            // Calculate total amount
            $totalRetailPrice = $input['final_amount'] ?? 0;

            if (!isset($input['final_amount']) && isset($input['metadata'])) {
                $totalRetailPrice = 0;

                foreach ($input['metadata'] as $pkg) {
                    if ($pkg['is_addon'] === true && $pkg['is_addon_included'] === false) {
                        continue;
                    }

                    $packageRetail = 0;
                    foreach ($pkg['products'] as $product) {
                        $supplyPrice = 0;
                        if ($product['pivot']['includeSupply']) {
                            $supplyPrice = ($product['provisioning']['supply']['retail_price'] * $product['pivot']['quantity']) ?? 0;
                        } else {
                            $supplyPrice = ($product['provisioning']['supply']['retail_price'] - $product['provisioning']['supply']['excluded_price']) ?? 0;
                        }

                        $installPrice = 0;
                        if ($product['pivot']['includeInstall']) {
                            $installPrice = ($product['provisioning']['install']['retail_price'] * $product['pivot']['quantity']) ?? 0;
                        } else {
                            $installPrice = ($product['provisioning']['install']['retail_price'] - $product['provisioning']['install']['excluded_price']) ?? 0;
                        }

                        $packageRetail += ($supplyPrice + $installPrice);
                    }

                    $totalRetailPrice += $packageRetail * ($pkg['quantity']  ?? 1);
                }
            }

            // Override the input total_amount
            $input['total_amount'] = $totalRetailPrice;

            // Create the Order
            $order = Order::create($input);

            // Create OrderQuotation with incremented version
            $latestQuotation = OrderQuotation::where('order_id', $order->id)->orderBy('version', 'desc')->first();
            $nextVersion = $latestQuotation ? ($latestQuotation->version + 1) : 1;

            if ($input['quotation_id'] === '0') {
                // Skip finding the quotation and set default values for 'quotation_id' and 'quotation_name'
                $quotationId = null;
                $quotationName = 'Custom Quotation';
            } else {
                $quotation = Quotation::find($input['quotation_id']);
                $quotationId = $quotation->id;
                $quotationName = $quotation->name;
            }

            OrderQuotation::create([
                'order_id' => $order->id,
                'quotation_id' => $quotationId,
                'quotation_name' => $quotationName,
                'version' => $nextVersion,
                'total_amount' => $input['total_amount'] + $bonusValue,
                'bonus' => $input['bonus'],
                'metadata' => json_encode($input['metadata']) ?? null,
            ]);


            return $this->sendResponse(new OrderResource($order), 'Order added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::find($id);

        if (is_null($order)) {
            return $this->sendError('Order not found.');
        }

        return $this->sendResponse(new OrderResource($order), 'Order retrieved successfully.');
    }

    public function showOwnerOrder($id)
    {
        $user = Auth::user();

        // Find the order by ID
        $order = Order::find($id);

        // Check if the order exists
        if (is_null($order)) {
            return $this->sendError('Order not found.');
        }

        // Check if the order's status is 'unreleased'
        if ($order->status === 'unreleased') {
            return $this->sendError('Order not found.');
        }

        // Check if the order belongs to the user based on phone number
        if ($order->user->phone_no !== $user->phone_no) {
            return $this->sendError('Invalid Credential');
        }

        // If the order exists and belongs to the user, return the order data
        return $this->sendResponse(new OwnerOrderResource($order), 'Order retrieved successfully.');
    }

    public function getOrderOverviewHead($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $mobile = $this->normalizePhoneNumber($order->user->phone_no);
        $lastFourMobile = substr($mobile, -4);

        return response()->json([
            'status' => 'success',
            'orderId' => $orderId,
            'mobileLast' => $lastFourMobile,
            'mobH' => Crypt::encryptString($mobile)
        ]);
    }

    public function showOrderOverview($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $user = Auth::user();

        $mobile = $this->normalizePhoneNumber($order->user->phone_no);
        $lastFourMobile = substr($mobile, -4);

        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'orderId' => $orderId,
                'mobileLast' => $lastFourMobile,
                'mobH' => Crypt::encryptString($mobile)
            ]);
        }

        $userMobile = $this->normalizePhoneNumber($user->phone_no);

        if ($mobile !== $userMobile) {
            return response()->json([
                'status' => 'invalid_auth',
                'orderId' => $orderId,
                'mobileLast' => $lastFourMobile,
                'mobH' => Crypt::encryptString($mobile)
            ]);
        }

        return $this->sendResponse(new OrderResource($order), 'Order retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        try {
            $input = $request->all();

            $validator = Validator::make($input, [
                'user_id' => 'nullable|numeric|max:255',
                'property_id' => 'nullable|numeric|min:0',
                'quotation_id' => 'nullable|numeric|min:0',
                'final_amount' => 'nullable|numeric|min:0',
                'unit_type' => 'nullable|string|max:255',
                'block' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'unit_no' => 'nullable|string|max:255',
                'single_bedroom_count' => 'nullable|numeric|min:0',
                'queen_bedroom_count' => 'nullable|numeric|min:0',
                'studio_count' => 'nullable|numeric|min:0',
                'bathroom_count' => 'nullable|numeric|min:1',
                'description' => 'nullable|string|max:0',
                'internal_remark' => 'nullable|string|min:0',
                'completion_day' => 'nullable|numeric|min:0',
                'tenure' => 'nullable|numeric|min:0',
                'metadata' => 'nullable',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $validatedData = $validator->validated();
            $validatedData['bedroom_count'] = $input['single_bedroom_count'] + $input['queen_bedroom_count'] + $input['studio_count'];

            $bonusValue = isset($input['bonus']['value']) && !empty($input['bonus']['value']) && (float)$input['bonus']['value'] != 0
                ? (float)$input['bonus']['value']
                : 0;

            // Handle bonus logic
            if (isset($input['bonus']) && is_array($input['bonus'])) {
                if (empty($input['bonus']['value']) || $input['bonus']['value'] == 0) {
                    $input['bonus'] = null;
                } else {
                    $input['bonus'] = json_encode($input['bonus']);
                }
            } else {
                $input['bonus'] = null;
            }

            $isDraftMode = empty($validatedData['user_id']);

            // Handle Draft Numbering - FIXED: Include ALL records (even soft deleted)
            if ($isDraftMode) {
                if (!Str::startsWith($order->order_no, 'DRAFT-')) {
                    // Use withTrashed() to include soft deleted records
                    $lastDraftOrder = Order::withTrashed() // Include soft deleted records
                        ->where('order_no', 'like', 'DRAFT-%')
                        ->orderBy('id', 'desc')
                        ->first();

                    $lastDraftNumber = $lastDraftOrder ? ((int)substr($lastDraftOrder->order_no, -5)) : 0;

                    // Generate unique draft number with collision check
                    do {
                        $lastDraftNumber++;
                        $newDraftNumber = 'DRAFT-' . now()->format('y') . str_pad($lastDraftNumber, 5, '0', STR_PAD_LEFT);

                        $exists = Order::withTrashed() // Include soft deleted records in collision check
                            ->where('order_no', $newDraftNumber)
                            ->where('id', '!=', $order->id) // Exclude current order
                            ->exists();
                    } while ($exists);

                    $order->order_no = $newDraftNumber;
                }
            } elseif (!$isDraftMode && Str::startsWith($order->order_no, 'DRAFT-')) {
                // Store original draft number
                if (!$order->draft_order_no) {
                    $order->draft_order_no = $order->order_no;
                }

                // Convert Draft to Confirmed Order - FIXED: Include ALL records
                $lastConfirmedOrder = Order::withTrashed() // Include soft deleted records
                    ->where('order_no', 'like', 'QUO-%')
                    ->orderBy('order_no', 'desc')
                    ->first();

                $lastConfirmedNumber = $lastConfirmedOrder ? ((int)substr($lastConfirmedOrder->order_no, -5)) : 0;

                // Generate unique confirmed order number with collision check
                do {
                    $lastConfirmedNumber++;
                    $newOrderNumber = 'QUO-' . now()->format('y') . str_pad($lastConfirmedNumber, 5, '0', STR_PAD_LEFT);

                    $exists = Order::withTrashed() // Include soft deleted records in collision check
                        ->where('order_no', $newOrderNumber)
                        ->exists();
                } while ($exists);

                $order->order_no = $newOrderNumber;
                $order->status = 'confirmed';
            }

            // Calculate total amount
            $totalRetailPrice = $input['final_amount'] ?? 0;

            if (!isset($input['final_amount']) && isset($input['metadata'])) {
                $totalRetailPrice = 0;

                foreach ($input['metadata'] as $pkg) {
                    if ($pkg['is_addon'] === true && $pkg['is_addon_included'] === false) {
                        continue;
                    }

                    $packageRetail = 0;
                    foreach ($pkg['products'] as $product) {
                        $supplyPrice = 0;
                        if ($product['pivot']['includeSupply']) {
                            $supplyPrice = ($product['provisioning']['supply']['retail_price'] * $product['pivot']['quantity']) ?? 0;
                        } else {
                            $supplyPrice = ($product['provisioning']['supply']['retail_price'] - $product['provisioning']['supply']['excluded_price']) ?? 0;
                        }

                        $installPrice = 0;
                        if ($product['pivot']['includeInstall']) {
                            $installPrice = ($product['provisioning']['install']['retail_price'] * $product['pivot']['quantity']) ?? 0;
                        } else {
                            $installPrice = ($product['provisioning']['install']['retail_price'] - $product['provisioning']['install']['excluded_price']) ?? 0;
                        }

                        $packageRetail += ($supplyPrice + $installPrice);
                    }

                    $totalRetailPrice += $packageRetail * ($pkg['quantity'] ?? 1);
                }
            }

            // Update order properties
            $order->user_id = $validatedData['user_id'];
            $order->property_id = $validatedData['property_id'];
            $order->total_amount = $totalRetailPrice;
            $order->final_amount = $input['final_amount'];
            $order->unit_type = $validatedData['unit_type'];
            $order->block = $validatedData['block'];
            $order->floor = $validatedData['floor'];
            $order->unit_no = $validatedData['unit_no'];
            $order->bedroom_count = $validatedData['bedroom_count'];
            $order->single_bedroom_count = $validatedData['single_bedroom_count'];
            $order->queen_bedroom_count = $validatedData['queen_bedroom_count'];
            $order->studio_count = $validatedData['studio_count'];
            $order->bathroom_count = $validatedData['bathroom_count'];
            $order->include_partition = $input['include_partition'];
            $order->is_progressive_payment = $input['is_progressive_payment'];
            $order->is_be_powered = $input['is_be_powered'];
            $order->installment_method = $input['installment_method'] ?? 'dynamic';
            $order->installment_amount = $input['installment_amount'] ?? 0;
            $order->be_powered_base_price = $input['be_powered_base_price'] ?? 0;
            $order->description = $validatedData['description'];
            $order->internal_remark = $validatedData['internal_remark'];
            $order->completion_day = $validatedData['completion_day'];
            $order->tenure = $validatedData['tenure'];

            // Ensure status is set properly
            if (!isset($validatedData['status'])) {
                $order->status = $isDraftMode ? 'draft' : 'unreleased';
            }

            // Create OrderQuotation with incremented version
            $latestQuotation = OrderQuotation::where('order_id', $order->id)->orderBy('version', 'desc')->first();
            $nextVersion = $latestQuotation ? ($latestQuotation->version + 1) : 1;

            OrderQuotation::create([
                'order_id' => $order->id,
                'quotation_id' => $validatedData['quotation_id'],
                'quotation_name' => $latestQuotation->quotation_name,
                'version' => $nextVersion,
                'total_amount' => $order->total_amount,
                'bonus' => $input['bonus'],
                'metadata' => json_encode($validatedData['metadata']) ?? null,
            ]);

            $order->save();

            return $this->sendResponse(new OrderResource($order), 'Order updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }

    public function updateInternalRemark(Request $request, $id)
    {
        $input = $request->all();

        $order = Order::find($id);

        $order->internal_remark = $input['internal_remark'];
        $order->save();

        return $this->sendResponse(new OrderResource($order), 'Internal Remark updated successfully.');
    }

    public function updateOwnerAddonPackages(Request $request, $id)
    {

        try {
            $input = $request->all();

            $latestQuotation = $input['latest_quotation'];

            $updatedLatestQuotation = OrderQuotation::find($latestQuotation['id']);
            $updatedLatestQuotation->metadata = $latestQuotation['metadata'];

            $updatedLatestQuotation->save();

            return $this->sendResponse(new OwnerOrderResource(Order::find($id)), 'Addon Packages updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }

    public function toggleOwnerAddonPackage($id, $package_id)
    {
        try {
            $order = Order::where('id', $id)->first();
            if (!$order) {
                return $this->sendError('Order not found', [], 404);
            }

            $updatedLatestQuotation = $order->orderQuotations()->latest()->first();
            if (!$updatedLatestQuotation) {
                return $this->sendError('Order quotation not found', [], 404);
            }

            // Decode the metadata JSON string into an array
            $metadata = json_decode($updatedLatestQuotation->metadata, true);
            if (!is_array($metadata)) {
                return $this->sendError('Invalid metadata format', [], 400);
            }

            // Find and toggle the package with matching package_id
            $packageFound = false;
            foreach ($metadata as $index => $package) {
                if (isset($package['id']) && $package['id'] == $package_id) {
                    // Toggle the is_addon_included field
                    $package['is_addon_included'] = !isset($package['is_addon_included']) ? true : !$package['is_addon_included'];
                    $metadata[$index] = $package;
                    $packageFound = true;
                    break;
                }
            }

            if (!$packageFound) {
                return $this->sendError("Package with ID {$package_id} not found in metadata", [], 404);
            }

            // Save the updated metadata back to the model
            $updatedLatestQuotation->metadata = json_encode($metadata);
            $updatedLatestQuotation->save();

            return $this->sendResponse(new OwnerOrderResource(Order::find($id)), 'Addon Packages updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }

    public function toggleIsBePowered($id)
    {
        $order = Order::find($id);

        if (is_null($order)) {
            return $this->sendError('Order not found.');
        }

        $order->is_be_powered = !$order->is_be_powered;
        $order->save();

        return $this->sendResponse('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (is_null($order)) {
            return $this->sendError('Order not found.');
        }

        // Delete associated OrderQuotations
        OrderQuotation::where('order_id', $order->id)->delete();

        // Now delete the Order
        $order->delete();

        return $this->sendResponse([], 'Order deleted successfully.');
    }


    public function confirmOrder($id)
    {
        try {
            // Find the order by ID
            $order = Order::find($id);

            if ($order) {
                // Update the order status
                $order->status = 'confirmed';
                $order->confirmed_at = now();
                $order->save(); // Use save() to persist changes

                // Get the latest sales number
                $lastSaleId = Sale::max('id') ?? 0;

                // Generate new sales number
                $input['sales_no'] = 'RSO-' . now()->format('y') . str_pad($lastSaleId + 1, 5, '0', STR_PAD_LEFT);

                // Get the latest quotation
                $latestQuotation = $order->orderQuotations()->latest()->first();

                if ($order->final_amount) {
                    $totalAmount = $order->final_amount; // Use final_amount if available
                } else {
                    // Parse metadata from the latest quotation (assumed to be JSON)
                    $packages = json_decode($latestQuotation->metadata, true);

                    // Calculate total amount, excluding add-ons where is_addon_included === false
                    $totalAmount = array_reduce($packages, function ($carry, $package) {
                        // Skip if it's an add-on and not included
                        if (
                            isset($package['is_addon']) && $package['is_addon'] === true &&
                            isset($package['is_addon_included']) && $package['is_addon_included'] === false
                        ) {
                            return $carry;
                        }

                        // Calculate package total
                        $packageTotal = array_reduce($package['products'], function ($sum, $product) {
                            $quantity = $product['pivot']['quantity'] ?? 1;

                            // Supply price
                            $supplyPrice = 0;
                            if ($product['pivot']['includeSupply']) {
                                $supplyPrice = ($product['provisioning']['supply']['retail_price'] * $quantity) ?? 0;
                            } else {
                                $supplyPrice = ($product['provisioning']['supply']['retail_price'] -
                                    $product['provisioning']['supply']['excluded_price']) ?? 0;
                            }

                            // Install price
                            $installPrice = 0;
                            if ($product['pivot']['includeInstall']) {
                                $installPrice = ($product['provisioning']['install']['retail_price'] * $quantity) ?? 0;
                            } else {
                                $installPrice = ($product['provisioning']['install']['retail_price'] -
                                    $product['provisioning']['install']['excluded_price']) ?? 0;
                            }

                            return $sum + $supplyPrice + $installPrice;
                        }, 0) * ($package['quantity'] ?? 1);

                        return $carry + $packageTotal;
                    }, 0);
                }

                // If bonus exists, subtract it from the total amount
                $bonus = json_decode($latestQuotation->bonus);
                $bonusValue = $bonus->value ?? 0;
                $totalAmount -= $bonusValue;

                // CREATE NEW SALE
                $sale = Sale::create([
                    'sales_no' => $input['sales_no'],
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'description' => '',
                    'total_amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'remaining_percentage' => 1,
                ]);

                // Ckeck for same property
                $foundedOrder = Order::where('property_id', $order->property_id)
                    ->where('block', $order->block)
                    ->where('floor', $order->floor)
                    ->where('unit_no', $order->unit_no)
                    ->first();

                Log::info($foundedOrder->sale);

                if ($foundedOrder) {
                    $foundedRenoSale = DB::table('reno_sales')
                        ->where('sale_id', $foundedOrder->sale->id)
                        ->first();

                    if ($foundedRenoSale) {
                        DB::table('reno_sales')->insert([
                            'reno_progress_id' => $foundedRenoSale->reno_progress_id,
                            'sale_id' => $sale->id,
                            'is_main' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // TODO: Create/Update Inventory

                return $this->sendResponse(['order_id' => $order->id, 'quotation_no' => $order->order_no], 'Order Confirmed');
            } else {
                return $this->sendError('Order Not Found.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error confirming order.', $th->getMessage());
        }
    }


    public function releaseOrder($id)
    {
        try {
            // Find the order by ID
            $order = Order::find($id);

            if ($order) {
                // Update the order status
                $order->status = 'released';
                $order->released_at = now();
                $order->save(); // Use save() to persist changes

                return $this->sendResponse([], 'Order Released');
            } else {
                return $this->sendError('Order Not Found.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error releasing order.', $th->getMessage());
        }
    }

    public function reReleaseOrder($id)
    {
        try {
            // Find the order by ID
            $order = Order::find($id);

            if ($order) {
                // Update the order status
                $order->status = 'released';
                $order->released_at = now();
                $order->save(); // Use save() to persist changes

                return $this->sendResponse([], 'Order Released');
            } else {
                return $this->sendError('Order Not Found.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error releasing order.', $th->getMessage());
        }
    }

    public function voidOrder($id)
    {
        try {
            // Find the order by ID
            $order = Order::find($id);

            if ($order) {
                // Update the order status
                $order->status = 'voided';
                $order->save(); // Use save() to persist changes

                return $this->sendResponse([], 'Order Voided');
            } else {
                return $this->sendError('Order Not Found.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error voiding order.', $th->getMessage());
        }
    }

    /**
     * Generate a new sales number based on the latest sales number.
     *
     * @param string|null $latestSaleNo
     * @return string
     */
    private function generateNewSalesNo($latestSaleNo)
    {
        if ($latestSaleNo) {
            // Extract the numeric part and increment
            preg_match('/(\d+)/', $latestSaleNo, $matches);
            $nextNumber = (int)$matches[0] + 1; // Increment the number
        } else {
            $nextNumber = 1; // Start from 1 if there are no sales
        }

        return 'S' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT); // Pad to 6 digits
    }


    private function normalizePhoneNumber($phoneNumber)
    {
        // Remove non-numeric characters
        $normalized = preg_replace('/\D/', '', $phoneNumber);

        // If it starts with '601' (international code), convert to local format
        if (strpos($normalized, '601') === 0) {
            // Convert '+601' to '011' or similar
            $normalized = '0' . substr($normalized, 3);
        }

        return $normalized;
    }
}
