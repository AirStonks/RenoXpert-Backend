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
        Schema::table('products', function (Blueprint $table) {
            $table->string('internal_desc')->nullable()->after('task_weightage');
            $table->string('depth')->nullable()->after('task_weightage');
            $table->string('height')->nullable()->after('task_weightage');
            $table->string('width')->nullable()->after('task_weightage');
            $table->string('material')->nullable()->after('task_weightage');
            $table->string('color')->nullable()->after('task_weightage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
