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
        Schema::create('registration_forms', function (Blueprint $table) {
            $table->id();
            $table->string('salutations')->nullable();
            $table->string('name_first')->nullable();
            $table->string('name_last')->nullable();
            $table->string('name_preferred')->nullable();
            $table->string('email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('ic')->nullable();
            $table->string('property_name')->nullable();
            $table->string('other_property_name')->nullable();
            $table->string('block')->nullable();
            $table->string('level')->nullable();
            $table->string('unit')->nullable();
            $table->string('layout_type')->nullable();
            $table->string('sqft')->nullable();
            $table->json('metadata')->nullable();
            $table->json('attachments')->nullable();
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
        Schema::dropIfExists('registration_forms');
    }
};
