<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountFeeResource;
use App\Models\DiscountFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountFeeController extends BaseController
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
        
        $type = $request->input('type');

        // Build the query to retrieve products
        $query = DiscountFee::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // Assuming 'name' is the field you want to search, adjust as necessary
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        // Apply type filter if provided
        if (!empty($type)) {
            $query->where('type', $type);
        }

        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'name');

        // Apply sorting if a sort field is provided
        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Paginate the results
        $discountFees = $query->paginate($size);

        // Custome response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $discountFees->currentPage(),  // Current page number
            "pageCount" => $discountFees->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $discountFees->total(),  // Total number of items
            "data" => DiscountFeeResource::collection($discountFees) // Transformed product data
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
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'amount' => 'nullable|numeric|min:0',
                'percentage' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $input['percentage'] /= 100;

            // Create the DiscountFee
            $discountFee = DiscountFee::create($input);

            return $this->sendResponse(new DiscountFeeResource($discountFee), 'Discount Fee added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DiscountFee $discountFee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiscountFee $discountFee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscountFee $discountFee)
    {
        //
    }
}
