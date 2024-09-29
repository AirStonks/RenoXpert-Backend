<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderQuotation;
use Illuminate\Http\Request;
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

            OrderQuotation::create([
                'order_id' => $order->id,
                'quotation_id' => $input['quotation_id'],
                'version' => $nextVersion,
                'total_amount' => 1000.00, // CHANGE IT LATER TO REAL DATA
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

            // Create OrderQuotation with incremented version
            $latestQuotation = OrderQuotation::where('order_id', $order->id)->orderBy('version', 'desc')->first();
            $nextVersion = $latestQuotation ? ($latestQuotation->version + 1) : 1;

            OrderQuotation::create([
                'order_id' => $order->id,
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
    public function destroy(Order $order)
    {
        //
    }
}
