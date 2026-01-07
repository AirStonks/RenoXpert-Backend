<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('inventory_items')) {
            // Table doesn't exist, create it
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // Table exists, add missing columns only
            Schema::table('inventory_items', function (Blueprint $table) {
                // Check and add created_by if it doesn't exist
                if (!Schema::hasColumn('inventory_items', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('id');
                }
                
                // Check and add updated_by if it doesn't exist
                if (!Schema::hasColumn('inventory_items', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
                
                // Check and add timestamps if they don't exist
                if (!Schema::hasColumn('inventory_items', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->after('updated_by');
                }
                
                if (!Schema::hasColumn('inventory_items', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
                
                // Check and add soft deletes if it doesn't exist
                if (!Schema::hasColumn('inventory_items', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable()->after('updated_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop the table if we created it (not if it already existed)
        // For safety, we'll only drop columns we added, not the entire table
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                // Only drop columns if they exist and were added by this migration
                $columns = ['deleted_at', 'updated_at', 'created_at', 'updated_by', 'created_by'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('inventory_items', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

