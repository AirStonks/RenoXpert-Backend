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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('alert_level')->default(0);
            $table->integer('total_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('coming_stock')->default(0);
            $table->integer('total_available_stock')->default(0);
            $table->integer('total_required_stock')->default(0);
            $table->integer('utilized_stock')->default(0);
            $table->integer('required_stock')->default(0);
            $table->integer('current_balance')->default(0);
            $table->integer('total_balance')->default(0);
            $table->string('status_')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
