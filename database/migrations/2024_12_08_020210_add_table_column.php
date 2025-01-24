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
        // Staging: done
        // PRD: done
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->integer('completion_day')->default(0)->after('total_amount');
        // });

        // Staging: done
        // PRD: done
        Schema::table('job_tasks', function (Blueprint $table) {
            $table->string('area')->nullable()->after('task_weightage');
        });
    }

    public function down()
    {
        //
    }
};
