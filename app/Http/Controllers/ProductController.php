<?php

// app/http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;
use App\Models\ProductInstall;
use App\Models\ProductSupply;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Retrieve the size parameter from the request with a default value of 5
        $size = $request->input('size', 5);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Build the query to retrieve products
        $query = Product::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // Assuming 'name' is the field you want to search, adjust as necessary
            $query->where('name', 'like', '%' . $search . '%');
        }


        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'name');

        // Apply sorting if a sort field is provided
        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Paginate the results
        $products = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $products->currentPage(),  // Current page number
            "pageCount" => $products->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $products->total(),  // Total number of items
            "data" => ProductResource::collection($products) // Transformed product data
        ];

        return response()->json($response, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'required|string|max:255',
                'SKU' => 'nullable|string',
                'pm_category' => 'required|numeric',
                'type' => 'required|string',
                'description' => 'nullable|string',
                'status' => 'nullable|string',
                'uom' => 'required|string',
                'task_weightage' => 'nullable|numeric',
                'color' => 'nullable|string',
                'material' => 'nullable|string',
                'width' => 'nullable|string',
                'height' => 'nullable|string',
                'depth' => 'nullable|string',
                'internal_desc' => 'nullable|string',
                'provisioning.supply.retail_price' => 'required|numeric|min:0',
                'provisioning.supply.cogs' => 'required|numeric|min:0',
                'provisioning.supply.excluded_price' => 'required|numeric|min:0',
                'provisioning.install.retail_price' => 'required|numeric|min:0',
                'provisioning.install.cogs' => 'required|numeric|min:0',
                'provisioning.install.excluded_price' => 'required|numeric|min:0',
            ]);
            // Apply unique validation for SKU only if it's provided
            if (!empty($input['SKU'])) {
                $validator->sometimes('SKU', 'unique:products,SKU', function ($input) {
                    return !empty($input['SKU']);
                });
            }

            // Check if validation fails before attempting to access validated data
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Now we can safely validate and transform data
            $validatedData = $validator->validated();

            // Transform 'category' to 'category_id'
            $validatedData['pm_category_id'] = (int) $validatedData['pm_category'];
            unset($validatedData['pm_category']);

            // Create the product
            $product = Product::create($validatedData);

            // Create Product Supply
            $productSupply = ProductSupply::create([
                'product_id' => $product->id,
                'retail_price' => $validatedData['provisioning']['supply']['retail_price'],
                'cogs' => $validatedData['provisioning']['supply']['cogs'],
                'excluded_price' => $validatedData['provisioning']['supply']['excluded_price'],
            ]);

            // Create Product Install
            $productInstall = ProductInstall::create([
                'product_id' => $product->id,
                'retail_price' => $validatedData['provisioning']['install']['retail_price'],
                'cogs' => $validatedData['provisioning']['install']['cogs'],
                'excluded_price' => $validatedData['provisioning']['install']['excluded_price'],
            ]);

            return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $input = $request->all();

        // Validate input data
        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'SKU' => 'nullable|string',
            'pm_category_id' => 'required|numeric',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'uom' => 'required|string',
            'task_weightage' => 'nullable|numeric',
            'color' => 'nullable|string',
            'material' => 'nullable|string',
            'width' => 'nullable|string',
            'height' => 'nullable|string',
            'depth' => 'nullable|string',
            'internal_desc' => 'nullable|string',
            'provisioning.supply.retail_price' => 'required|numeric|min:0',
            'provisioning.supply.cogs' => 'required|numeric|min:0',
            'provisioning.supply.excluded_price' => 'required|numeric|min:0',
            'provisioning.install.retail_price' => 'required|numeric|min:0',
            'provisioning.install.cogs' => 'required|numeric|min:0',
            'provisioning.install.excluded_price' => 'required|numeric|min:0',
        ]);

        // Apply unique validation for SKU only if it's provided
        if (!empty($input['SKU'])) {
            $validator->sometimes('SKU', 'unique:products,SKU', function ($input) {
                return !empty($input['SKU']);
            });
        }

        // Check if validation fails before attempting to access validated data
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        // Update the product
        $product->fill($validatedData);
        $product->productSupply->retail_price = $validatedData['provisioning']['supply']['retail_price'];
        $product->productSupply->cogs = $validatedData['provisioning']['supply']['cogs'];
        $product->productSupply->excluded_price = $validatedData['provisioning']['supply']['excluded_price'];
        $product->productInstall->retail_price = $validatedData['provisioning']['install']['retail_price'];
        $product->productInstall->cogs = $validatedData['provisioning']['install']['cogs'];
        $product->productInstall->excluded_price = $validatedData['provisioning']['install']['excluded_price'];

        // Save the updated product
        $product->save();
        $product->productSupply->save();
        $product->productInstall->save();

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);

        $product->productSupply->delete();
        $product->productInstall->delete();
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
