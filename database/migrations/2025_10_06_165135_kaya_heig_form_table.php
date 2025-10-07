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
        Schema::create('kaya_heig_forms', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('mobile_number');
            $table->string('email');
            $table->string('tower');
            $table->string('floor');
            $table->string('unit_type');
            $table->string('units_owned');
            $table->json('rental_plan');
            $table->json('concerns');
            $table->json('support_needed');
            $table->string('preferred_contact');
            $table->string('preferred_time')->nullable();
            $table->string('aditional_info')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaya_heig_forms');
    }
};
