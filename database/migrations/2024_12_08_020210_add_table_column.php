<?php

use App\Models\Property\Property;
use App\Models\Property\PropertyROI;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Staging: Done
        Schema::table('reno_progress', function (Blueprint $table) {
            $table->string('status')->default('pending-vp')->change();
        });

        // Staging: Done
        Schema::create('project_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reno_progress_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('unit')->nullable();
            $table->string('status')->nullable();
            $table->double('payment_percentage')->nullable();
            $table->integer('snapshot_year')->nullable();
            $table->integer('snapshot_week')->nullable();
            $table->date('snapshot_date')->nullable();
            $table->string('snapshot_type')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reno_progress_id')->references('id')->on('reno_progress')->onDelete('set null');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
        });
    }

    public function down()
    {
        // 
    }
};
