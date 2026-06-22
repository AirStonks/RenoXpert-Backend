<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $database = DB::getDatabaseName();
        $indexes = DB::select(
            "SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'campaign_packages'
             AND COLUMN_NAME = 'order_id' AND NON_UNIQUE = 0",
            [$database]
        );
        foreach ($indexes as $idx) {
            DB::statement("ALTER TABLE `campaign_packages` DROP INDEX `{$idx->INDEX_NAME}`");
        }
    }

    public function down(): void
    {
        // Intentionally NOT restoring the unique index: duplicate order_id links are now allowed by design.
    }
};
