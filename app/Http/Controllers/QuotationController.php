<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuotationController extends BaseController
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
        $query = Quotation::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        $quotations = $query->paginate($size);

        // Custome response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $quotations->currentPage(),  // Current page number
            "pageCount" => $quotations->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $quotations->total(),  // Total number of items
            "data" => QuotationResource::collection($quotations) // Transformed product data
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

            $validator = Validator::make($input, [
                'name' => 'required|string|max:255',
                'total_amount' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:255',
                'valid_from' => 'nullable|string|max:255',
                'valid_until' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            if (isset($input['metadata'])) {
                $input['metadata'] = json_encode($input['metadata']);
            }

            $quotation = Quotation::create($input);


            return $this->sendResponse(new QuotationResource($quotation), 'Quotation added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quotation = Quotation::find($id);

        if (is_null($quotation)) {
            return $this->sendError('Package not found.');
        }

        return $this->sendResponse(new QuotationResource($quotation), 'Product retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        //
    }
}
