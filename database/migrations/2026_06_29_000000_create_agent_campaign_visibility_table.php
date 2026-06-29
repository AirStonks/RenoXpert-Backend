<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_campaign_visibility', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('campaign_id');
            $table->timestamps();

            // No DB-level foreign keys (hand-managed-table convention) — plain indexes only.
            $table->unique(['user_id', 'campaign_id']);
            $table->index('user_id');
            $table->index('campaign_id');
        });

        if (Schema::hasColumn('campaigns', 'visible_to_agents')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('visible_to_agents');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('campaigns', 'visible_to_agents')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->boolean('visible_to_agents')->default(false)->after('status');
            });
        }

        Schema::dropIfExists('agent_campaign_visibility');
    }
};
