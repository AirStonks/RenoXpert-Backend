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
     * Adds type field to inventory_item_variants and populates it from products.type
     */
    public function up(): void
    {
        if (Schema::hasTable('inventory_item_variants')) {
            // Add type column if it doesn't exist
            if (!Schema::hasColumn('inventory_item_variants', 'type')) {
        Schema::table('inventory_item_variants', function (Blueprint $table) {
                    $table->string('type')->nullable()->after('variant_name');
                });
            }

            // Populate type from products table
            // Update variants that have product_id matching products.id
            // product_id is stored as varchar, so we need to cast it for comparison
            $variants = DB::table('inventory_item_variants')
                ->whereNull('type')
                ->orWhere('type', '')
                ->get();
            
            foreach ($variants as $variant) {
                if (!empty($variant->product_id)) {
                    $product = DB::table('products')
                        ->where('id', (int) $variant->product_id)
                        ->first();
                    
                    if ($product && !empty($product->type)) {
                        DB::table('inventory_item_variants')
                            ->where('id', $variant->id)
                            ->update(['type' => $product->type]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventory_item_variants')) {
        Schema::table('inventory_item_variants', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_item_variants', 'type')) {
                    $table->dropColumn('type');
                }
        });
        }
    }
};
