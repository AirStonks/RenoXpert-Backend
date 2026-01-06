<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check current state of the table
        $columns = DB::select("SHOW COLUMNS FROM inventory_item_variants LIKE 'alert%'");
        $columnNames = array_column($columns, 'Field');
        
        // Step 1: Drop old index on alert_status if it exists
        $indexes = DB::select("SHOW INDEX FROM inventory_item_variants WHERE Key_name LIKE '%alert_status%'");
        if (!empty($indexes)) {
            Schema::table('inventory_item_variants', function (Blueprint $table) {
                $table->dropIndex(['alert_status']);
            });
        }
        
        // Step 2: If alert_level_temp doesn't exist, create it
        if (!in_array('alert_level_temp', $columnNames)) {
            Schema::table('inventory_item_variants', function (Blueprint $table) {
                $table->integer('alert_level_temp')->default(0)->nullable()->after('total_balance');
            });
            // Set all values to 0
            DB::statement("UPDATE inventory_item_variants SET alert_level_temp = 0");
        }
        
        // Step 3: If alert_level exists as varchar, drop it
        if (in_array('alert_level', $columnNames)) {
            Schema::table('inventory_item_variants', function (Blueprint $table) {
                $table->dropColumn('alert_level');
            });
        }
        
        // Step 4: If alert_status still exists, drop it
        if (in_array('alert_status', $columnNames)) {
            Schema::table('inventory_item_variants', function (Blueprint $table) {
                $table->dropColumn('alert_status');
            });
        }
        
        // Step 5: Rename temp column to alert_level
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            $table->renameColumn('alert_level_temp', 'alert_level');
        });
        
        // Step 6: Add new status column and index
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            $table->string('status')->default('active')->nullable()->after('alert_level');
            $table->index('alert_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            // Drop status column
            $table->dropColumn('status');
            
            // Drop index on alert_level
            $table->dropIndex(['alert_level']);
        });
        
        // Add temporary string column
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            $table->string('alert_status_temp')->nullable()->after('total_balance');
        });
        
        // Copy values (convert integer to string)
        DB::statement("UPDATE inventory_item_variants SET alert_status_temp = CAST(alert_level AS CHAR)");
        
        // Drop alert_level column
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            $table->dropColumn('alert_level');
        });
        
        // Rename temp column back to alert_status
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            $table->renameColumn('alert_status_temp', 'alert_status');
        });
        
        // Re-add index on alert_status
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            $table->index('alert_status');
        });
    }
};

