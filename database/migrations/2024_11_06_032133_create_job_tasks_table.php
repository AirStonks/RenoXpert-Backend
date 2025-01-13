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
        Schema::create('job_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('priority')->default(1);
            $table->integer('task_weightage')->default(1);
            $table->boolean('is_supplied')->default(false);
            $table->boolean('is_installed')->default(false);
            $table->timestamp('supply_date')->nullable();
            $table->timestamp('install_date')->nullable();
            $table->boolean('is_defect_form')->default(false);
            $table->boolean('is_qc_form')->default(false);
            $table->boolean('is_key_form')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->string('owner_comment')->nullable();
            $table->string('internal_comment')->nullable();
            $table->string('status');
            $table->json('attachments')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('job_id')->references('id')->on('phase_jobs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_tasks');
    }
};
