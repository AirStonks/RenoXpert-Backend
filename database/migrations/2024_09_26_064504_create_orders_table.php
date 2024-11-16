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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('block')->nullable();
            $table->string('floor')->nullable();
            $table->string('unit_no')->nullable();
            $table->integer('bedroom_count')->nullable();
            $table->integer('bathroom_count')->nullable();
            $table->double('total_amount')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
