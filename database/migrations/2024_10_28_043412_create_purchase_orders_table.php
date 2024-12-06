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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_no')->unique();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->double('total_amount')->nullable();
            $table->timestamp('shipping_date')->nullable();
            $table->timestamp('shipped_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('delivered_date')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->string('order_status')->default('pending');
            $table->string('description')->nullable();
            $table->string('internal_note')->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
};
