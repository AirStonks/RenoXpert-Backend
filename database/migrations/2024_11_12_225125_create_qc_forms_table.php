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
        Schema::create('qc_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reno_progress_id')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('inspector_first_name')->nullable();
            $table->string('inspector_last_name')->nullable();
            $table->string('inspector_role')->nullable();
            $table->string('contractor_email')->nullable();
            $table->string('property_name')->nullable();
            $table->string('other_property_name')->nullable();
            $table->string('block')->nullable();
            $table->string('level')->nullable();
            $table->string('unit')->nullable();
            $table->integer('bedroom_count')->nullable();
            $table->integer('bathroom_count')->nullable();
            $table->boolean('include_commune_living')->default(1);
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_forms');
    }
};
