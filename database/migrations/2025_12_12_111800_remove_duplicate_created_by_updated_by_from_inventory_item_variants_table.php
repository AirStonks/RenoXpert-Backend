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
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            // Remove duplicate created_by and updated_by columns
            // (created_at and updated_at from timestamps() are sufficient)
            if (Schema::hasColumn('inventory_item_variants', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('inventory_item_variants', 'updated_by')) {
                $table->dropColumn('updated_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_item_variants', function (Blueprint $table) {
            // Restore the columns if rollback is needed
            $table->unsignedBigInteger('created_by')->nullable()->after('rack_no');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });
    }
};
