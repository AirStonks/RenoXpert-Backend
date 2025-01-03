<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
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

        // Build the query to retrieve inventory
        $query = Inventory::query();

        // Include soft-deleted records
        $query->withTrashed();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                // Search across individual fields
                $query->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results
        $form = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $form->currentPage(),  // Current page number
            "pageCount" => $form->lastPage(), // Total number of pages
            "sortField" => null,              // Sorting field, if applicable
            "sortOrder" => null,              // Sorting order, if applicable
            "totalCount" => $form->total(),  // Total number of items
            "data" => InventoryResource::collection($form->items()) // Transformed property data
        ];

        return response()->json($response, 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
