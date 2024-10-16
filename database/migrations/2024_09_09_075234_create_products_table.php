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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('SKU')->unique()->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->string('internal_remark')->nullable();
            $table->double('product_retail_price')->nullable();
            $table->double('product_cost_of_good_sold')->nullable();
            $table->double('product_excluded_price')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
