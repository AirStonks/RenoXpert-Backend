<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends BaseController
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
        $query = Package::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        $packages = $query->paginate($size);

        // Custome response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $packages->currentPage(),  // Current page number
            "pageCount" => $packages->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $packages->total(),  // Total number of items
            "data" => PackageResource::collection($packages) // Transformed product data
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
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $package = Package::create($input);

            $totalAmount = 0.0;

            foreach ($input['products'] as $productInput) {

                $product = Product::find($productInput['id']);

                $totalAmount += $product->product_retail_price * $productInput['quantity'];

                $package->products()->attach($productInput['id'], ['quantity' => $productInput['quantity']]);
            }

            $package->total_price = $totalAmount;

            $package->save();

            $package->products;

            return $this->sendResponse(new PackageResource($package), 'Package added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::find($id);

        if (is_null($package)) {
            return $this->sendError('Package not found.');
        }

        return $this->sendResponse(new PackageResource($package), 'Product retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {

        try {
            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $validatedData = $validator->validated();

            $package->name = $validatedData['name'];
            $package->description = $validatedData['description'];

            // OPTION 1: Remove all associated product_package and Insert the newest product_package

            $package->products()->detach();

            $totalAmount = 0.0;

            foreach ($input['products'] as $productInput) {

                $product = Product::find($productInput['id']);

                $totalAmount += $product->product_retail_price * $productInput['quantity'];

                $package->products()->attach($productInput['id'], ['quantity' => $productInput['quantity']]);
            }

            $package->total_price = $totalAmount;

            $package->save();

            // OPTION 2: Check for update for all product_package with previous new product_package, if found changed, update it.

            return $this->sendResponse(new PackageResource($package), 'Package added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return $this->sendResponse([], 'Package not found.', 404);
        }

        // Detach any related items (or whatever the pivot model is)
        $package->products()->detach(); // Adjust the method name as necessary

        // Delete the package
        $package->delete();

        return $this->sendResponse([], 'Package deleted successfully.');
    }
}
