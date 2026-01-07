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
        if (!Schema::hasTable('inventory_items')) {
            return; // Table doesn't exist, skip this migration
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('inventory_items', 'item_name')) {
                $table->string('item_name')->after('id');
            }
            
            if (!Schema::hasColumn('inventory_items', 'description')) {
                $table->text('description')->nullable()->after('item_name');
            }
            
            if (!Schema::hasColumn('inventory_items', 'type')) {
                $table->string('type')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('inventory_items', 'status')) {
                $table->string('status')->nullable()->after('type');
            }
        });

        // Add indexes only if columns exist and indexes don't exist
        $indexExists = function ($columnName) {
            try {
                $result = DB::select("
                    SELECT COUNT(*) as count 
                    FROM information_schema.statistics 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'inventory_items' 
                    AND column_name = ?
                ", [$columnName]);
                return $result[0]->count > 0;
            } catch (\Exception $e) {
                return false;
            }
        };

        Schema::table('inventory_items', function (Blueprint $table) use ($indexExists) {
            if (Schema::hasColumn('inventory_items', 'item_name') && !$indexExists('item_name')) {
                $table->index('item_name');
            }
            
            if (Schema::hasColumn('inventory_items', 'type') && !$indexExists('type')) {
                $table->index('type');
            }
            
            if (Schema::hasColumn('inventory_items', 'status') && !$indexExists('status')) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex(['item_name']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'description', 'type', 'status']);
        });
    }
};

