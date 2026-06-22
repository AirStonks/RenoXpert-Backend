<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $database = DB::getDatabaseName();

        // The UNIQUE index on order_id is what blocks linking one template quotation
        // to multiple campaign packages. But that same index also backs the foreign key
        // (order_id -> orders.id), so MySQL refuses to drop it (error 1553) unless another
        // index covers order_id first. So: ensure a NON-unique index exists on order_id,
        // then drop the unique index(es). The FK then uses the non-unique replacement.
        $uniqueIndexes = DB::select(
            "SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'campaign_packages'
             AND COLUMN_NAME = 'order_id' AND NON_UNIQUE = 0",
            [$database]
        );

        if (empty($uniqueIndexes)) {
            return; // already non-unique (e.g. re-run) — nothing to do
        }

        $replacement = 'campaign_packages_order_id_index';
        $hasReplacement = DB::select(
            "SELECT 1 FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'campaign_packages'
             AND INDEX_NAME = ? LIMIT 1",
            [$database, $replacement]
        );
        if (empty($hasReplacement)) {
            DB::statement("ALTER TABLE `campaign_packages` ADD INDEX `{$replacement}` (`order_id`)");
        }

        foreach ($uniqueIndexes as $idx) {
            if ($idx->INDEX_NAME === $replacement) {
                continue; // never drop the non-unique replacement
            }
            DB::statement("ALTER TABLE `campaign_packages` DROP INDEX `{$idx->INDEX_NAME}`");
        }
    }

    public function down(): void
    {
        // Intentionally NOT restoring the unique index: duplicate order_id links are now allowed by design.
    }
};
