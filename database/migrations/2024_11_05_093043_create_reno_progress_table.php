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
        Schema::create('reno_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('status');
            $table->timestamp('contractual_end_date')->nullable();
            $table->timestamp('contractual_start_date')->nullable();
            $table->timestamp('contractual_p1_start_date')->nullable();
            $table->timestamp('contractual_p1_end_date')->nullable();
            $table->timestamp('contractual_p2_start_date')->nullable();
            $table->timestamp('contractual_p2_end_date')->nullable();
            $table->timestamp('contractual_qc_start_date')->nullable();
            $table->timestamp('contractual_qc_end_date')->nullable();
            $table->timestamp('contractual_pc_start_date')->nullable();
            $table->timestamp('contractual_pc_end_date')->nullable();
            $table->timestamp('contractual_handover_date')->nullable();
            $table->timestamp('contractor_end_date')->nullable();
            $table->timestamp('contractor_start_date')->nullable();
            $table->timestamp('contractor_p1_start_date')->nullable();
            $table->timestamp('contractor_p1_end_date')->nullable();
            $table->timestamp('contractor_p2_start_date')->nullable();
            $table->timestamp('contractor_p2_end_date')->nullable();
            $table->timestamp('contractor_qc_start_date')->nullable();
            $table->timestamp('contractor_qc_end_date')->nullable();
            $table->timestamp('contractor_pc_start_date')->nullable();
            $table->timestamp('contractor_pc_end_date')->nullable();
            $table->timestamp('contractor_handover_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reno_progress');
    }
};
