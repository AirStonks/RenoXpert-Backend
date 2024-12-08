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
        Schema::create('po_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_desc')->nullable();
            $table->string('sku')->nullable();
            $table->integer('qty')->default(0);
            $table->boolean('supply')->default(false);
            $table->boolean('install')->default(false);
            $table->double('unit_price')->nullable();
            $table->double('total_price')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('shipping_date')->nullable();
            $table->timestamp('shipped_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('delivered_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_items');
    }
};
