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
     * Drops inventory_items table and recreates it with the exact same structure as inventories table
     */
    public function up(): void
    {
        // Get all foreign keys that reference inventory_items
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME, TABLE_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = 'inventory_items' 
            AND TABLE_SCHEMA = DATABASE()
        ");

        // Drop all foreign keys that reference inventory_items
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        // Drop the inventory_items table
        Schema::dropIfExists('inventory_items');

        // Recreate inventory_items with the exact same structure as inventories
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id(); // bigint unsigned, auto_increment (same as inventories)
            $table->unsignedBigInteger('product_id')->nullable(); // Same as inventories
            $table->integer('alert_level')->default(0); // Same as inventories
            $table->integer('total_stock')->default(0); // Same as inventories
            $table->integer('current_stock')->default(0); // Same as inventories
            $table->integer('coming_stock')->default(0); // Same as inventories
            $table->integer('total_available_stock')->default(0); // Same as inventories
            $table->integer('total_required_stock')->default(0); // Same as inventories
            $table->integer('utilized_stock')->default(0); // Same as inventories
            $table->integer('required_stock')->default(0); // Same as inventories
            $table->integer('current_balance')->default(0); // Same as inventories
            $table->integer('total_balance')->default(0); // Same as inventories
            $table->enum('status_', ['in_development'])->default('in_development'); // Same as inventories
            $table->timestamps(); // Same as inventories
            $table->softDeletes(); // Same as inventories

            // Foreign key constraint (same as inventories)
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * This will drop the recreated inventory_items table
     * Note: Foreign keys will not be restored automatically
     */
    public function down(): void
    {
        // Get all foreign keys that reference inventory_items
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME, TABLE_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = 'inventory_items' 
            AND TABLE_SCHEMA = DATABASE()
        ");

        // Drop all foreign keys that reference inventory_items
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        Schema::dropIfExists('inventory_items');
    }
};
