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
        Schema::table('inventories', function (Blueprint $table) {
            // Remove duplicate created_by and updated_by columns
            // (created_at and updated_at from timestamps() are sufficient)
            if (Schema::hasColumn('inventories', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('inventories', 'updated_by')) {
                $table->dropColumn('updated_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Restore the columns if rollback is needed
            $table->unsignedBigInteger('created_by')->nullable()->after('status_');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });
    }
};
