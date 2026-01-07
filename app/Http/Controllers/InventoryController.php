<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use App\Models\InventoryItemVariant;
use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class InventoryController extends BaseController
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

        // Retrieve filter parameters (check multiple possible parameter names)
        $status = $request->input('status') ?? $request->input('filter.status');
        $type = $request->input('type') ?? $request->input('filter.type');

        // Whitelist allowed sort fields to prevent SQL injection
        $allowedSortFields = ['id', 'name', 'type', 'status', 'created_at', 'updated_at'];
        $sortField = $request->input('sortField', 'id');
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'id';
        
        // Validate sort order
        $sortOrder = strtolower($request->input('sortOrder', 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        // Build the query to retrieve inventory items
        $query = InventoryItem::with('variants');

        // Apply status filter if provided
        if (!empty($status)) {
            $statusLower = strtolower(trim($status));
            if ($statusLower !== 'all status' && $statusLower !== 'all' && $statusLower !== '') {
                // Use original status for exact database match
                $query->where('status', $status);
            }
        }

        // Apply type filter if provided
        // Check both inventory_item type and variant type
        if (!empty($type)) {
            $typeLower = strtolower(trim($type));
            if ($typeLower !== 'loose items' && $typeLower !== 'all' && $typeLower !== '') {
                // Normalize type: "Loose Items" -> "loose_items", "Renovation" -> "renovation"
                $normalizedType = strtolower(str_replace(' ', '_', $type));
                
                $query->where(function ($query) use ($normalizedType, $type) {
                    // Check parent type (exact match or normalized)
                    $query->where('inventory_items.type', $normalizedType)
                        ->orWhere('inventory_items.type', $type)
                        // Also check if any variant has this type
                        ->orWhereHas('variants', function ($q) use ($normalizedType, $type) {
                            $q->where('type', $normalizedType)
                              ->orWhere('type', $type);
                        });
                });
            }
        }

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // Escape LIKE wildcards for safety
            $searchEscaped = addcslashes($search, '%_');
            $query->where(function ($query) use ($searchEscaped) {
                $query->where('name', 'like', '%' . $searchEscaped . '%')
                    ->orWhere('type', 'like', '%' . $searchEscaped . '%')
                    ->orWhere('status', 'like', '%' . $searchEscaped . '%');
            });
        }

        // Apply sorting if a sort field is provided
        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Paginate the results
        $inventoryItems = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $inventoryItems->currentPage(),
            "pageCount" => $inventoryItems->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $inventoryItems->total(),
            "data" => InventoryItemResource::collection($inventoryItems)
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreInventoryItemRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            DB::beginTransaction();
            
            $inventoryItem = InventoryItem::create($validatedData);

            DB::commit();

            return $this->sendResponse(new InventoryItemResource($inventoryItem), 'Inventory item created successfully.');
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
        $inventoryItem = InventoryItem::with('variants')->find($id);

        if (is_null($inventoryItem)) {
            return $this->sendError('Inventory item not found.');
        }

        return $this->sendResponse(new InventoryItemResource($inventoryItem), 'Inventory item retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventory): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            DB::beginTransaction();

            $inventory->fill($validatedData);
            $inventory->save();

            DB::commit();

            return $this->sendResponse(new InventoryItemResource($inventory->load('variants')), 'Inventory item updated successfully.');
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

            $inventoryItem = InventoryItem::find($id);

            if (is_null($inventoryItem)) {
                DB::rollBack();
                return $this->sendError('Inventory item not found.');
            }

            $inventoryItem->delete();

            DB::commit();

            return $this->sendResponse([], 'Inventory item deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset inventory data by setting columns to NULL or default values.
     * Does NOT delete rows, only clears the data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetData(): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Reset inventory_item_variants table
            DB::table('inventory_item_variants')->update([
                'product_id' => null,
                'variant_name' => null,
                'type' => null,
                'sku' => null,
                'description' => null,
                'in_stock' => 0,
                'projected_stock' => 0,
                'utilised_stock' => 0,
                'incoming_stock' => 0,
                'supply_price' => 0,
                'install_price' => 0,
                'total_balance' => 0,
                'alert_level' => 0,
                'status' => 'active',
                'color' => null,
                'width' => null,
                'height' => null,
                'depth' => null,
                'material' => null,
                'zone' => null,
                'shelf_level' => null,
                'rack_no' => null,
            ]);

            // Reset inventory_items table (only update columns that actually exist)
            DB::table('inventory_items')->update([
                'product_id' => null,
                'name' => '', // 'name' is NOT NULL, so set to empty string
                'sku' => null,
                'description' => null,
                'type' => null,
                'status' => null,
                'zone' => null,
            ]);

            DB::commit();

            return $this->sendResponse([], 'Inventory data reset successfully. All rows preserved, data cleared.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ], 500);
        }
    }

    /**
     * Fetch inventory data from external API and sync to inventory_item_variants table.
     * Clears inventory_item table first, then fills inventory_item_variants from API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncFromExternalApi(Request $request): JsonResponse
    {
        $apiUrl = null;
        try {
            DB::beginTransaction();

            // Step 1: Clear inventory_item table first (only update columns that actually exist)
            DB::table('inventory_items')->update([
                'product_id' => null,
                'name' => '', // 'name' is NOT NULL, so set to empty string
                'sku' => null,
                'description' => null,
                'type' => null,
                'status' => null,
                'zone' => null,
            ]);

            // Get pagination parameters
            $size = $request->input('size', 10);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $sortField = $request->input('sortField', '');
            $sortOrder = $request->input('sortOrder', 'asc');

            // Get API configuration from config (not env() for better caching support)
            $apiUrl = config('inventory.api_url');
            $bearerToken = config('inventory.api_bearer_token');
            
            // Validate required configuration
            if (empty($apiUrl)) {
                throw new \Exception('INVENTORY_API_URL configuration is required');
            }
            if (empty($bearerToken)) {
                throw new \Exception('INVENTORY_API_BEARER_TOKEN configuration is required');
            }

            // Build API URL with pagination parameters
            $apiUrlWithParams = $apiUrl . '?page=' . $page . '&size=' . $size . '&search=' . urlencode($search) . '&sortField=' . urlencode($sortField) . '&sortOrder=' . urlencode($sortOrder);

            // Create HTTP client
            $client = new Client([
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Make API request
            $response = $client->get($apiUrlWithParams);
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Extract data from API response
            $apiData = $responseBody['data'] ?? [];
            $totalCount = $responseBody['total'] ?? $responseBody['totalCount'] ?? count($apiData);
            $currentPage = $responseBody['page'] ?? $page;
            $lastPage = $responseBody['lastPage'] ?? $responseBody['pageCount'] ?? ceil($totalCount / $size);

            $syncedVariants = [];

            // Process each item from API response - ONLY fill inventory_item_variants
            foreach ($apiData as $item) {
                try {
                    $productId = (string) ($item['id'] ?? null);
                    $sku = $item['SKU'] ?? null;
                    $variantName = $item['name'] ?? 'Unnamed Variant';

                    if (empty($productId)) {
                        continue;
                    }

                    // Get or create a minimal inventory_item (required foreign key)
                    $inventoryItem = DB::table('inventory_items')
                        ->whereNull('product_id')
                        ->where('name', '')
                        ->first();

                    if (!$inventoryItem) {
                        $inventoryItemId = DB::table('inventory_items')->insertGetId([
                            'product_id' => null,
                            'name' => '', // 'name' is NOT NULL, so set to empty string
                            'sku' => null,
                            'description' => null,
                            'type' => null,
                            'status' => null,
                            'zone' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $inventoryItemId = $inventoryItem->id;
                    }

                    // Create or update inventory_item_variant (use product_id as key since sku can be null)
                    InventoryItemVariant::updateOrCreate(
                        ['product_id' => $productId],
                        [
                            'inventory_item_id' => $inventoryItemId,
                            'product_id' => $productId,
                            'variant_name' => $variantName,
                            'sku' => $sku, // Can be null if API doesn't provide SKU
                            'type' => $item['type'] ?? null,
                            'description' => $item['description'] ?? null,
                            'supply_price' => $item['supply_price'] ?? 0,
                            'install_price' => $item['install_price'] ?? 0,
                            'color' => $item['color'] ?? null,
                            'material' => $item['material'] ?? null,
                            'width' => !empty($item['width']) ? (float) $item['width'] : null,
                            'height' => !empty($item['height']) ? (float) $item['height'] : null,
                            'depth' => !empty($item['depth']) ? (float) $item['depth'] : null,
                            'in_stock' => 0,
                            'projected_stock' => 0,
                            'utilised_stock' => 0,
                            'incoming_stock' => 0,
                            'total_balance' => 0,
                            'alert_level' => 0,
                            'status' => 'active',
                            // zone is inherited from parent inventory_item automatically
                            'shelf_level' => null,
                            'rack_no' => null,
                        ]
                    );

                    $syncedVariants[] = $sku;
                } catch (\Exception $e) {
                    Log::error('Error syncing variant', [
                        'item' => $item,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            DB::commit();

            // Custom response
            $response = [
                "page" => $currentPage,
                "pageCount" => $lastPage,
                "sortField" => $sortField,
                "sortOrder" => $sortOrder,
                "totalCount" => $totalCount,
                "data" => [
                    'synced_variants_count' => count($syncedVariants),
                    'message' => 'Successfully synced ' . count($syncedVariants) . ' inventory item variants from external API',
                ]
            ];

            return response()->json($response, 200);
        } catch (RequestException $e) {
            DB::rollBack();
            Log::error('External API request failed', [
                'url' => $apiUrl ?? 'N/A',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ]);

            return $this->sendError('External API Error.', [
                'message' => 'Failed to fetch data from external API: ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error in syncFromExternalApi', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return $this->sendError('Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ], 500);
        }
    }
}
