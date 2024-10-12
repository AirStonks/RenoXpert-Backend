<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderQuotation;
use App\Models\Quotation;
use App\Models\Sale;
use Illuminate\Http\Request;
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
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            // Validate the input
            $validator = Validator::make($input, [
                'contact_id' => 'required|numeric|max:255',
                'property_id' => 'nullable|numeric|min:0',
                'quotation_id' => 'nullable|numeric|min:0',
                'block' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'unit_no' => 'nullable|string|max:255',
                'total_amount' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:255',
                'metadata' => 'nullable|array', // Added validation for metadata
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Generate order number
            $today = date('Ymd');
            $lastOrder = Order::where('order_no', 'like', 'OR-' . $today . '%')->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? (intval(substr($lastOrder->order_no, -4)) + 1) : 1; // Increment or start at 1

            $input['order_no'] = 'OR-' . $today . str_pad($sequence, 4, '0', STR_PAD_LEFT); // Format the sequence to 4 digits

            // Create the Order
            $order = Order::create($input);

            // Create OrderQuotation with incremented version
            $latestQuotation = OrderQuotation::where('order_id', $order->id)->orderBy('version', 'desc')->first();
            $nextVersion = $latestQuotation ? ($latestQuotation->version + 1) : 1;

            $quotation = Quotation::find($input['quotation_id']);

            OrderQuotation::create([
                'order_id' => $order->id,
                'quotation_id' => $input['quotation_id'],
                'quotation_name' => $quotation->name,
                'version' => $nextVersion,
                'total_amount' => $input['total_amount'],
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

    public function showOrderOverview($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $mobile = $order->contact->phone_no;
        $sessionValid = $this->checkSession($mobile, $orderId);

        if ($sessionValid) {
            return $this->sendResponse(new OrderResource($order), 'Order retrieved successfully.');
        } else {
            return response()->json([
                'status' => 'otp_required',
                'message' => 'OTP Required',
                'order_id' => $orderId,
                'mobile' => $mobile
            ], 200);
        }
    }

    public function verify($orderId)
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $mobileNumber = $order->contact->phone_no;

        // Re-verify the guest with the mobile number associated with the order
        if ($this->verifyGuestOtp($mobileNumber)) {
            // If the re-verification is successful, return a response indicating access to the order detail
            return $this->sendResponse(new OrderResource($order), 'Order retrieved successfully.');
        }

        // If the re-verification fails, return an error response
        return response()->json(['error' => 'Invalid mobile number or OTP'], 401);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        try {
            $input = $request->all();

            $validator = Validator::make($input, [
                'contact_id' => 'required|numeric|max:255',
                'property_id' => 'nullable|numeric|min:0',
                'quotation_id' => 'nullable|numeric|min:0',
                'block' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'unit_no' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'metadata' => 'nullable|array', // Added validation for metadata
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $validatedData = $validator->validated();

            $order->contact_id = $validatedData['contact_id'];
            $order->property_id = $validatedData['property_id'];
            $order->block = $validatedData['block'];
            $order->floor = $validatedData['floor'];
            $order->unit_no = $validatedData['unit_no'];
            $order->description = $validatedData['description'];
            $order->status = 'pending';

            // Create OrderQuotation with incremented version
            $latestQuotation = OrderQuotation::where('order_id', $order->id)->orderBy('version', 'desc')->first();
            $nextVersion = $latestQuotation ? ($latestQuotation->version + 1) : 1;

            $quotation = Quotation::find($input['quotation_id']);

            OrderQuotation::create([
                'order_id' => $order->id,
                'quotation_id' => $validatedData['quotation_id'],
                'quotation_name' => $quotation->name,
                'version' => $nextVersion,
                'total_amount' => 1000.00, // CHANGE IT LATER TO REAL DATA
                'metadata' => json_encode($input['metadata']) ?? null,
            ]);

            $order->save();

            return $this->sendResponse(new OrderResource($order), 'Order updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
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
                $newSalesNo = $this->generateNewSalesNo($latestSaleNo);

                // CREATE NEW SALE
                Sale::create([
                    'sales_no' => $newSalesNo,
                    'order_id' => $order->id,
                    'user_id' => null,
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

    private function verifyGuestOtp($mobileNumber)
    {
        // Verify the guest OTP using the provided mobile number
        $storedOtp = session('guest_otp_' . $mobileNumber);
        $requestOtp = $request->input('otp_code');
        return Hash::check($requestOtp, $storedOtp);
    }

}
