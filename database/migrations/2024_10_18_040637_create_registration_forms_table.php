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
            $table->string('block')->nullable();
            $table->string('level')->nullable();
            $table->string('unit')->nullable();
            $table->string('layout_type')->nullable();
            $table->string('sqft')->nullable();
            $table->string('quest_1')->nullable();
            $table->string('quest_2')->nullable();
            $table->string('quest_3')->nullable();
            $table->string('quest_4')->nullable();
            $table->string('quest_5')->nullable();
            $table->string('quest_6')->nullable();
            $table->string('quest_7')->nullable();
            $table->string('quest_8')->nullable();
            $table->string('status')->default('pending');
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
