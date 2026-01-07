<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryItemVariantResource;
use App\Models\InventoryItemVariant;
use App\Http\Requests\StoreInventoryItemVariantRequest;
use App\Http\Requests\UpdateInventoryItemVariantRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryItemVariantController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Validate and sanitize pagination parameter
        $size = min(max((int) $request->input('size', 5), 1), 100); // Between 1 and 100

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Whitelist allowed sort fields to prevent SQL injection
        $allowedSortFields = ['id', 'variant_name', 'sku', 'type', 'product_id', 'inventory_item_id', 'created_at', 'updated_at'];
        $sortField = $request->input('sortField', 'id');
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'id';
        
        // Validate sort order
        $sortOrder = strtolower($request->input('sortOrder', 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        // Build the query to retrieve inventory item variants
        $query = InventoryItemVariant::with('inventoryItem');

        // Filter by inventory_item_id if provided
        $inventoryItemId = $request->input('inventory_item_id');
        if (!empty($inventoryItemId)) {
            $query->where('inventory_item_id', (int) $inventoryItemId);
        }

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // Escape LIKE wildcards for safety
            $searchEscaped = addcslashes($search, '%_');
            $query->where(function ($query) use ($searchEscaped) {
                $query->where('variant_name', 'like', '%' . $searchEscaped . '%')
                    ->orWhere('sku', 'like', '%' . $searchEscaped . '%')
                    ->orWhere('type', 'like', '%' . $searchEscaped . '%')
                    ->orWhere('product_id', 'like', '%' . $searchEscaped . '%');
            });
        }

        // Apply sorting if a sort field is provided
        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Paginate the results
        $variants = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $variants->currentPage(),
            "pageCount" => $variants->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $variants->total(),
            "data" => InventoryItemVariantResource::collection($variants)
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreInventoryItemVariantRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Remove zone from validated data - it's read-only and inherited from parent
            unset($validatedData['zone']);

            DB::beginTransaction();

            $variant = InventoryItemVariant::create($validatedData);

            DB::commit();

            return $this->sendResponse(new InventoryItemVariantResource($variant->load('inventoryItem')), 'Inventory item variant created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Validation Error.', $e->errors(), 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
            ], 500);
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
        $variant = InventoryItemVariant::with('inventoryItem')->find($id);

        if (is_null($variant)) {
            return $this->sendError('Inventory item variant not found.');
        }

        return $this->sendResponse(new InventoryItemVariantResource($variant), 'Inventory item variant retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateInventoryItemVariantRequest $request, InventoryItemVariant $inventoryVariant): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // Remove zone from validated data - it's read-only and inherited from parent
            unset($validatedData['zone']);

            DB::beginTransaction();

            $inventoryVariant->fill($validatedData);
            $inventoryVariant->save();

            DB::commit();

            return $this->sendResponse(new InventoryItemVariantResource($inventoryVariant->load('inventoryItem')), 'Inventory item variant updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Validation Error.', $e->errors(), 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $variant = InventoryItemVariant::find($id);

            if (is_null($variant)) {
                DB::rollBack();
                return $this->sendError('Inventory item variant not found.');
            }

            $variant->delete();

            DB::commit();

            return $this->sendResponse([], 'Inventory item variant deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

