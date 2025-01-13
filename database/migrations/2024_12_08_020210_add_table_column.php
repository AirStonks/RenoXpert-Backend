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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('task_weightage')->default(1)->change();
        });

        Schema::table('job_tasks', function (Blueprint $table) {
            $table->boolean('is_key_form')->default(false)->after('is_qc_form');
        });
    }

    public function down()
    {
        //
    }
};
