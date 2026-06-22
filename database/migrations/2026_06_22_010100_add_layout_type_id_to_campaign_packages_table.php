<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaign_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('layout_type_id')->nullable()->after('campaign_id');
            $table->index('layout_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_packages', function (Blueprint $table) {
            $table->dropIndex(['layout_type_id']);
            $table->dropColumn('layout_type_id');
        });
    }
};
