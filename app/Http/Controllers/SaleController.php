<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Http\Resources\SaleResourceHead;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends BaseController
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
        $query = Sale::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('sales_no', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        $sales = $query->paginate($size);

        // Custome response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $sales->currentPage(),  // Current page number
            "pageCount" => $sales->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $sales->total(),  // Total number of items
            "data" => $request->input('head') === 'true' ? SaleResourceHead::collection($sales) : SaleResource::collection($sales) // Transformed product data
        ];

        return response()->json($response, 200);
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
    public function show($id)
    {
        $sale = Sale::find($id);

        if (is_null($sale)) {
            return $this->sendError('Sale not found.');
        }

        return $this->sendResponse(new SaleResource($sale), 'Sale retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
