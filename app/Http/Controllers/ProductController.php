<?php

// app/http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;
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
                'SKU' => 'required|string|unique:products,SKU',
                'category' => 'required|numeric',
                'type' => 'required|string',
                'description' => 'nullable|string',
                'status' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'premium_price' => 'nullable|numeric|min:0',
            ], [
                'SKU.required' => 'The SKU field is required.',
                'SKU.unique' => 'The SKU has already been taken.',
            ]);

            $validatedData = $validator->validated();

            // Transform 'category' to 'category_id'
            $validatedData['category_id'] = (int) $validatedData['category'];
            unset($validatedData['category']);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $product = Product::create($validatedData);

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
            'SKU' => [
                'required',
                'string',
                Rule::unique('products', 'SKU')->ignore($product->id), // Ensure SKU is unique, but ignore current product's ID
            ],
            'category' => 'required|numeric',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'premium_price' => 'nullable|numeric|min:0',
        ], [
            'SKU.required' => 'The SKU field is required.',
        ]);

        $validatedData = $validator->validated();

        // Transform 'category' to 'category_id'
        $validatedData['category_id'] = (int) $validatedData['category'];
        unset($validatedData['category']);


        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Update the product
        $product->name = $validatedData['name'];
        $product->SKU = $validatedData['SKU'];
        $product->category_id = $validatedData['category_id'];
        $product->type = $validatedData['type'];
        $product->description = $validatedData['description'] ?? null;
        $product->price = $validatedData['price'];
        $product->premium_price = $validatedData['premium_price'] ?? null;
        $product->status = $validatedData['status'] ?? null;


        // Save the updated product
        $product->save();

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

        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
