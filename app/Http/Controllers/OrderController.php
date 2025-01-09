<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OwnerOrderResource;
use App\Models\Order;
use App\Models\OrderQuotation;
use App\Models\Quotation;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve the size parameter from the request with a default value of 10
        $size = $request->input('size', 10);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Build the query to retrieve product categories
        $query = Order::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('order_no', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'name');

        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        $orders = $query->paginate($size);

        // Custome response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $orders->currentPage(),  // Current page number
            "pageCount" => $orders->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $orders->total(),  // Total number of items
            "data" => OrderResource::collection($orders) // Transformed product data
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
                'user_id' => 'required|numeric|max:255',
                'form_id' => 'nullable',
                'property_id' => 'nullable|numeric|min:0',
                'quotation_id' => 'nullable|numeric|min:0',
                'block' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'unit_no' => 'nullable|string|max:255',
                'bedroom_count' => 'nullable',
                'bathroom_count' => 'nullable',
                'total_amount' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:255',
                'metadata' => 'nullable|array', // Added validation for metadata
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Get the last order's ID, or default to 0 if no orders exist
            $lastOrderId = Order::max('id') ?? 0;

            // Generate order number based on the last order's ID
            $input['order_no'] = 'QUO-' . now()->format('y') . str_pad($lastOrderId + 1, 5, '0', STR_PAD_LEFT);

            $bonusValue = isset($input['bonus']['value']) && !empty($input['bonus']['value']) && $input['bonus']['value'] != 0
                ? $input['bonus']['value']
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
            return $this->sendError('Error.', $th);
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
                'user_id' => 'required|numeric|max:255',
                'property_id' => 'nullable|numeric|min:0',
                'quotation_id' => 'nullable|numeric|min:0',
                'total_amount' => 'nullable|numeric|min:0',
                'block' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'unit_no' => 'nullable|string|max:255',
                'bedroom_count' => 'nullable|numeric|min:1',
                'bathroom_count' => 'nullable|numeric|min:1',
                'description' => 'nullable|string|max:255',
                'metadata' => 'nullable|array', // Added validation for metadata
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $validatedData = $validator->validated();

            $bonusValue = isset($input['bonus']['value']) && !empty($input['bonus']['value']) && $input['bonus']['value'] != 0
                ? $input['bonus']['value']
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

            $order->user_id = $validatedData['user_id'];
            $order->property_id = $validatedData['property_id'];
            $order->total_amount = $input['total_amount'] - $bonusValue;
            $order->block = $validatedData['block'];
            $order->floor = $validatedData['floor'];
            $order->unit_no = $validatedData['unit_no'];
            $order->bedroom_count = $validatedData['bedroom_count'];
            $order->bathroom_count = $validatedData['bathroom_count'];
            $order->description = $validatedData['description'];
            $order->status = 'pending';

            // Create OrderQuotation with incremented version
            $latestQuotation = OrderQuotation::where('order_id', $order->id)->orderBy('version', 'desc')->first();
            $nextVersion = $latestQuotation ? ($latestQuotation->version + 1) : 1;

            OrderQuotation::create([
                'order_id' => $order->id,
                'quotation_id' => $validatedData['quotation_id'],
                'quotation_name' => $latestQuotation->quotation_name,
                'version' => $nextVersion,
                'total_amount' => $order->total_amount + $bonusValue, // CHANGE IT LATER TO REAL DATA
                'bonus' => $input['bonus'],
                'metadata' => json_encode($input['metadata']) ?? null,
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
                $order->save(); // Use save() to persist changes

                // Get the latest sales number
                $latestSaleNo = Sale::orderBy('created_at', 'desc')->value('sales_no');

                // Generate new sales number
                $input['sales_no'] = 'RSO-' . now()->format('y') . str_pad(Sale::count() + 1, 5, '0', STR_PAD_LEFT);

                // CREATE NEW SALE
                Sale::create([
                    'sales_no' => $input['sales_no'],
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'sale_no' => $latestSaleNo,
                    'description' => '',
                    'total_amount' => $order->total_amount,
                    'remaining_amount' => $order->total_amount,
                    'remaining_percentage' => 1,
                ]);

                return $this->sendResponse([], 'Order Confirmed');
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
                $order->save(); // Use save() to persist changes

                return $this->sendResponse([], 'Order Released');
            } else {
                return $this->sendError('Order Not Found.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error releasing order.', $th->getMessage());
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
