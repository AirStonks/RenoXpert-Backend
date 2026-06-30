<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaign_layout_types', function (Blueprint $table) {
            $table->json('roi_calculator')->nullable()->after('rental_projection');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_layout_types', function (Blueprint $table) {
            $table->dropColumn('roi_calculator');
        });
    }
};
