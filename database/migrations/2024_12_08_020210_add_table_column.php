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
        // Schema::table('payments', function (Blueprint $table) {
        //     $table->json('attachments')->nullable()->after('payment_method');
        //     $table->string('remark')->nullable()->after('payment_method');
        //     $table->string('receiving_account')->nullable()->after('payment_method');
        //     $table->string('bank')->nullable()->after('payment_method');
        //     $table->string('payment_date')->nullable()->after('payment_method');
        //     $table->string('payment_channel')->nullable()->after('payment_method');
        // });

        Schema::table('quotation_packages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('quo_pkg_prods', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Schema::create('job_tasks', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('task_id')->nullable();
        //     $table->integer('space_number')->nullable();
        //     $table->integer('space_number')->nullable();
            
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->foreign('task_id')->references('id')->on('job_tasks')->onDelete('cascade');
        // });
    }

    public function down()
    {
        //
    }
};
