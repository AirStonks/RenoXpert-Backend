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
            $table->timestamp('contractual_handover_date')->nullable()->after('status');
            $table->timestamp('contractual_pc_end_date')->nullable()->after('status');
            $table->timestamp('contractual_pc_start_date')->nullable()->after('status');
            $table->timestamp('contractual_qc_end_date')->nullable()->after('status');
            $table->timestamp('contractual_qc_start_date')->nullable()->after('status');
            $table->timestamp('contractual_p2_end_date')->nullable()->after('status');
            $table->timestamp('contractual_p2_start_date')->nullable()->after('status');
            $table->timestamp('contractual_p1_end_date')->nullable()->after('status');
            $table->timestamp('contractual_p1_start_date')->nullable()->after('status');

            $table->timestamp('contractor_end_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_start_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_handover_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_pc_end_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_pc_start_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_qc_end_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_qc_start_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_p2_end_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_p2_start_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_p1_end_date')->nullable()->after('contractual_handover_date');
            $table->timestamp('contractor_p1_start_date')->nullable()->after('contractual_handover_date');
        });
        
        Schema::table('job_tasks', function (Blueprint $table) {
            $table->boolean('is_visible')->default(false)->after('is_qc_form');
        });
    }

    public function down()
    {
        //
    }
};
