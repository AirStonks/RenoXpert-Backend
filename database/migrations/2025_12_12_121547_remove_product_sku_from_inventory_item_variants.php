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
     * Removes product_sku column from inventory_item_variants table
     * since it's duplicated with sku field
     */
    public function up(): void
    {
        if (Schema::hasTable('inventory_item_variants')) {
        Schema::table('inventory_item_variants', function (Blueprint $table) {
                // Drop index on product_sku if it exists
                $indexExists = DB::select("
                    SELECT 1 FROM information_schema.statistics 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'inventory_item_variants' 
                    AND COLUMN_NAME = 'product_sku' 
                    AND INDEX_NAME != 'PRIMARY'
                ");
                
                if (!empty($indexExists)) {
                    $table->dropIndex(['product_sku']);
                }
                
                // Drop the product_sku column
                if (Schema::hasColumn('inventory_item_variants', 'product_sku')) {
                    $table->dropColumn('product_sku');
                }
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventory_item_variants')) {
        Schema::table('inventory_item_variants', function (Blueprint $table) {
                // Restore product_sku column
                $table->string('product_sku')->nullable()->after('product_id');
                $table->index('product_sku');
        });
        }
    }
};
