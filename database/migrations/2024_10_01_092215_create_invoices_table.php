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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('invoice_no');
            $table->double('percentage');
            $table->double('amount');
            $table->string('status')->default('pending');
            $table->JSON('discountsData')->nullable();
            $table->JSON('feesData')->nullable();
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
