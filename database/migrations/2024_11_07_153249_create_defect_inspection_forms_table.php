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
        Schema::create('defect_inspection_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reno_progress_id')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('property_name')->nullable();
            $table->string('other_property_name')->nullable();
            $table->string('block')->nullable();
            $table->string('level')->nullable();
            $table->string('unit')->nullable();
            $table->string('contractor_name')->nullable();
            $table->string('contractor_email')->nullable();
            $table->integer('bedroom_count')->nullable();
            $table->integer('bathroom_count')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            
            $table->foreign('reno_progress_id')->references('id')->on('reno_progress')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defect_inspection_forms');
    }
};
