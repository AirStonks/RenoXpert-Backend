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
        // Staging: Done
        // PRD: Done
        // Schema::table('users', function (Blueprint $table) {
        //     $table->string('country_code')->nullable()->after('status');
        // });

        // // Staging: Done
        // // POSTPONDED
        // Schema::table('quotation_packages', function (Blueprint $table) {
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // // POSTPONDED
        // Schema::table('quo_pkg_prods', function (Blueprint $table) {
        //     $table->softDeletes();
        // });


        // Staging: Done
        Schema::create('rpm_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reno_progress_id')->nullable();
            $table->string('job_category')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('reno_progress_id')->references('id')->on('reno_progress')->onDelete('cascade');
        });


        // Staging: Done
        Schema::create('rpm_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('space_type')->nullable();
            $table->string('room_name')->nullable();
            $table->string('item_name')->nullable();
            $table->integer('priority')->default(1);
            $table->integer('task_weightage')->default(1);
            $table->integer('sequence')->default(1);
            $table->boolean('is_visible')->default(false);
            $table->string('internal_comment')->nullable();
            $table->string('owner_comment')->nullable();
            $table->json('internal_attachments')->nullable();
            $table->json('owner_attachments')->nullable();
            $table->string('status')->default('not-started');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('job_id')->references('id')->on('rpm_jobs')->onDelete('cascade');
        });

        // Staging: Done
        Schema::create('rpm_task_qcs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->boolean('is_visible')->default(false);
            $table->string('internal_comment')->nullable();
            $table->string('owner_comment')->nullable();
            $table->json('internal_attachments')->nullable();
            $table->json('owner_attachments')->nullable();
            $table->string('status')->default('not-started');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('task_id')->references('id')->on('rpm_tasks')->onDelete('cascade');
        });
    }

    public function down()
    {
        // 
    }
};
