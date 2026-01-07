<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fixes the master-variant relationship:
     * 1. Recreates inventory_items with proper master table structure (general product info)
     * 2. Fixes inventory_item_variants foreign key to match inventory_items.id type (bigint)
     */
    public function up(): void
    {
        // Step 1: Drop foreign key from inventory_item_variants
        if (Schema::hasTable('inventory_item_variants')) {
            try {
                DB::statement("ALTER TABLE `inventory_item_variants` DROP FOREIGN KEY `inventory_item_variants_inventory_item_id_foreign`");
            } catch (\Exception $e) {
                // Foreign key might not exist or have different name
            }
        }

        // Step 2: Get all other foreign keys that reference inventory_items
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME, TABLE_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = 'inventory_items' 
            AND TABLE_SCHEMA = DATABASE()
            AND CONSTRAINT_NAME != 'inventory_item_variants_inventory_item_id_foreign'
        ");

        // Drop all other foreign keys that reference inventory_items
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        // Step 3: Drop inventory_items table
        Schema::dropIfExists('inventory_items');

        // Step 4: Recreate inventory_items as master table with general product information
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id(); // bigint unsigned, auto_increment
            
            // General product information (shared across all variants)
            $table->unsignedBigInteger('product_id')->nullable(); // Reference to external products
            $table->string('name'); // General product name (e.g., "Dining Table Set")
            $table->text('description')->nullable(); // General product description
            $table->string('type')->nullable(); // Product type/category (e.g., "carpentry", "component")
            $table->string('status')->nullable()->default('active'); // General product status
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Foreign key to products table
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            
            // Indexes for common queries
            $table->index('name');
            $table->index('type');
            $table->index('status');
        });

        // Step 5: Fix inventory_item_variants foreign key to match new inventory_items.id type
        if (Schema::hasTable('inventory_item_variants')) {
            // Check if inventory_item_id column exists and its current type
            $columnInfo = DB::select("
                SELECT DATA_TYPE 
                FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'inventory_item_variants' 
                AND COLUMN_NAME = 'inventory_item_id'
            ");
            
            if (!empty($columnInfo)) {
                $currentType = $columnInfo[0]->DATA_TYPE;
                
                if ($currentType === 'char' || $currentType === 'varchar') {
                    // Column exists but has wrong type (char/varchar with UUID values)
                    // Since we're recreating inventory_items, existing relationships are lost
                    // We cannot convert UUID strings directly to bigint, so we must:
                    // 1. Drop the column (this will lose the data, but relationships are already broken)
                    // 2. Recreate it with the correct type
                    
                    Schema::table('inventory_item_variants', function (Blueprint $table) {
                        $table->dropColumn('inventory_item_id');
                    });
                    
                    // Recreate the column with correct type (nullable to handle orphaned records)
                    Schema::table('inventory_item_variants', function (Blueprint $table) {
                        $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id');
                    });
                } elseif ($currentType !== 'bigint') {
                    // Column exists but is not bigint - drop and recreate
                    Schema::table('inventory_item_variants', function (Blueprint $table) {
                        $table->dropColumn('inventory_item_id');
                    });
                    
                    Schema::table('inventory_item_variants', function (Blueprint $table) {
                        $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id');
                    });
                } else {
                    // Column is already bigint, but might be NOT NULL - make it nullable
                    DB::statement("ALTER TABLE `inventory_item_variants` MODIFY `inventory_item_id` bigint unsigned NULL");
                }
            } else {
                // Column doesn't exist - add it as nullable
                Schema::table('inventory_item_variants', function (Blueprint $table) {
                    $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id');
                });
            }
            
            // Step 6: Handle orphaned records before adding foreign key
            // Since inventory_items was recreated (empty), all existing inventory_item_id values are orphaned
            // Set all orphaned records to NULL to preserve variant data
            $existingItemIds = DB::table('inventory_items')->pluck('id')->toArray();
            if (empty($existingItemIds)) {
                // No parent records exist, set all to NULL
                DB::table('inventory_item_variants')->update(['inventory_item_id' => null]);
            } else {
                // Set only orphaned records to NULL
                DB::table('inventory_item_variants')
                    ->whereNotIn('inventory_item_id', $existingItemIds)
                    ->whereNotNull('inventory_item_id')
                    ->update(['inventory_item_id' => null]);
            }
            
            // Step 7: Add foreign key constraint and index
            Schema::table('inventory_item_variants', function (Blueprint $table) {
                // Check if foreign key already exists
                $fkExists = DB::select("
                    SELECT 1 FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'inventory_item_variants' 
                    AND COLUMN_NAME = 'inventory_item_id' 
                    AND REFERENCED_TABLE_NAME = 'inventory_items'
                ");
                
                if (empty($fkExists)) {
                    // Foreign key allows NULL for orphaned records
                    $table->foreign('inventory_item_id')
                        ->references('id')
                        ->on('inventory_items')
                        ->onDelete('cascade');
                }
                
                // Ensure index exists
                $indexExists = DB::select("
                    SELECT 1 FROM information_schema.statistics 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'inventory_item_variants' 
                    AND COLUMN_NAME = 'inventory_item_id' 
                    AND INDEX_NAME != 'PRIMARY'
                    AND INDEX_NAME != 'inventory_item_variants_inventory_item_id_foreign'
                ");
                
                if (empty($indexExists)) {
                    $table->index('inventory_item_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key from inventory_item_variants
        if (Schema::hasTable('inventory_item_variants')) {
            try {
                DB::statement("ALTER TABLE `inventory_item_variants` DROP FOREIGN KEY `inventory_item_variants_inventory_item_id_foreign`");
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        }

        // Get all foreign keys that reference inventory_items
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME, TABLE_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = 'inventory_items' 
            AND TABLE_SCHEMA = DATABASE()
        ");

        // Drop all foreign keys
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Continue
            }
        }

        Schema::dropIfExists('inventory_items');
    }
};
