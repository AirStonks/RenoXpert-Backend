<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('reno_progress', function (Blueprint $table) {
            $table->timestamp('contractual_end_date')->nullable()->after('status');
            $table->timestamp('contractual_start_date')->nullable()->after('status');

            $table->timestamp('contractor_end_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_start_date')->nullable()->after('contractual_handover_date');
        });
    }

    public function down()
    {
        //
    }
};
