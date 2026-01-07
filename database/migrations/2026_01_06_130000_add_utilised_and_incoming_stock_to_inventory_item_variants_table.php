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
            // Add new stock tracking fields - keep defaults simple so existing data is safe
            if (!Schema::hasColumn('inventory_item_variants', 'utilised_stock')) {
                $table->integer('utilised_stock')->default(0)->after('projected_stock');
            }

            if (!Schema::hasColumn('inventory_item_variants', 'incoming_stock')) {
                $table->integer('incoming_stock')->default(0)->after('utilised_stock');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * NOTE: For live systems we avoid dropping columns to preserve data.
     */
    public function down(): void
    {
        // Intentionally left empty to avoid dropping columns in production.
    }
};


