<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migrates data from products table to inventory_item_variants table
     */
    public function up(): void
    {
        // Get all products (including soft-deleted ones to preserve state)
        $products = DB::table('products')->get();
        
        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($products as $product) {
            try {
                DB::beginTransaction();

                // 1. Find or create inventory_item for this product
                $inventoryItem = DB::table('inventory_items')
                    ->where('item_name', $product->name)
                    ->first();

                if (!$inventoryItem) {
                    // Create new inventory_item
                    $inventoryItemId = (string) Str::uuid();
                    
                    // Prepare inventory_item data with all required fields
                    $inventoryItemData = [
                        'id' => $inventoryItemId,
                        'item_name' => $product->name,
                        'product_name' => $product->name, // REQUIRED field from old structure
                        'sku' => $product->SKU ?? 'SKU-' . $product->id, // REQUIRED field from old structure
                        'description' => $product->description,
                        'type' => $product->type,
                        'status' => $product->status ?? 'active',
                        'created_at' => $product->created_at ?? now(),
                        'updated_at' => $product->updated_at ?? now(),
                        'deleted_at' => $product->deleted_at,
                    ];
                    
                    DB::table('inventory_items')->insert($inventoryItemData);
                    $inventoryItem = (object) ['id' => $inventoryItemId];
                }

                // 2. Check if variant already exists (by SKU)
                $existingVariant = null;
                if ($product->SKU) {
                    $existingVariant = DB::table('inventory_item_variants')
                        ->where('sku', $product->SKU)
                        ->first();
                }

                if ($existingVariant) {
                    $skippedCount++;
                    DB::rollBack();
                    continue;
                }

                // 3. Handle SKU - required field, must be unique
                $sku = $product->SKU;
                if (empty($sku)) {
                    // Generate unique SKU if product doesn't have one
                    $sku = 'PROD-' . $product->id . '-' . Str::random(6);
                    // Ensure uniqueness
                    while (DB::table('inventory_item_variants')->where('sku', $sku)->exists()) {
                        $sku = 'PROD-' . $product->id . '-' . Str::random(6);
                    }
                }

                // 4. Convert width, height, depth from varchar to decimal
                $width = $this->convertToDecimal($product->width);
                $height = $this->convertToDecimal($product->height);
                $depth = $this->convertToDecimal($product->depth);

                // 5. Create inventory_item_variant
                DB::table('inventory_item_variants')->insert([
                    'inventory_item_id' => $inventoryItem->id,
                    'product_id' => (string) $product->id, // Convert bigint to string
                    'variant_name' => $product->name, // REQUIRED
                    'type' => $product->type, // From products.type
                    'sku' => $sku, // REQUIRED, UNIQUE
                    'description' => $product->description,
                    'in_stock' => 0, // Default
                    'projected_stock' => 0, // Default
                    'supply_price' => 0.00, // Default
                    'install_price' => 0.00, // Default
                    'total_balance' => 0, // Default
                    'alert_level' => 0,
                    'status' => 'active',
                    'color' => $product->color,
                    'width' => $width,
                    'height' => $height,
                    'depth' => $depth,
                    'material' => $product->material,
                    'zone' => null,
                    'shelf_level' => null,
                    'rack_no' => null,
                    'created_at' => $product->created_at ?? now(),
                    'updated_at' => $product->updated_at ?? now(),
                    'deleted_at' => $product->deleted_at,
                ]);

                DB::commit();
                $successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;
                Log::error("Failed to migrate product ID {$product->id} to inventory_item_variant", [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Log summary
        Log::info("Products to Inventory Item Variants migration completed", [
            'success' => $successCount,
            'skipped' => $skippedCount,
            'errors' => $errorCount,
            'total' => $products->count()
        ]);
    }

    /**
     * Convert string to decimal, handling NULL and invalid values
     */
    private function convertToDecimal($value): ?float
    {
        if (empty($value) || $value === null) {
            return null;
        }

        // Remove any non-numeric characters except decimal point
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        
        if (empty($cleaned)) {
            return null;
        }

        $decimal = (float) $cleaned;
        
        // Return null if conversion resulted in 0 and original wasn't "0"
        if ($decimal == 0 && $value !== '0' && $value !== 0) {
            return null;
        }

        return $decimal;
    }

    /**
     * Reverse the migrations.
     * 
     * This will delete all inventory_item_variants that were created from products
     * Note: This will also delete the inventory_items if they were created during migration
     */
    public function down(): void
    {
        // Find all variants that have product_id matching products table
        $productIds = DB::table('products')->pluck('id')->map(fn($id) => (string) $id)->toArray();
        
        if (!empty($productIds)) {
            // Delete variants created from products
            DB::table('inventory_item_variants')
                ->whereIn('product_id', $productIds)
                ->delete();
            
            // Optionally delete inventory_items that were created during migration
            // (Only if they have no other variants)
            // This is commented out to be safe - uncomment if you want to clean up
            /*
            $inventoryItemIds = DB::table('inventory_item_variants')
                ->whereIn('product_id', $productIds)
                ->pluck('inventory_item_id')
                ->unique()
                ->toArray();
            
            foreach ($inventoryItemIds as $itemId) {
                $variantCount = DB::table('inventory_item_variants')
                    ->where('inventory_item_id', $itemId)
                    ->count();
                
                if ($variantCount == 0) {
                    DB::table('inventory_items')->where('id', $itemId)->delete();
                }
            }
            */
        }
    }
};
