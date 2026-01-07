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
        // Drop the broken table if it exists from a previous failed migration
        Schema::dropIfExists('inventory_item_variants');

        Schema::create('inventory_item_variants', function (Blueprint $table) {
            $table->id();
            // inventory_items.id is bigint unsigned (auto-increment), so we must match that type
            $table->unsignedBigInteger('inventory_item_id');
            $table->string('product_id')->nullable();
            $table->string('variant_name');
            $table->string('type')->nullable();
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->integer('in_stock')->default(0);
            $table->integer('projected_stock')->default(0);
            $table->decimal('supply_price', 12, 2)->default(0);
            $table->decimal('install_price', 12, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->string('alert_status')->nullable();
            $table->string('color')->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('depth', 10, 2)->nullable();
            $table->string('material')->nullable();
            $table->string('shelf_level')->nullable();
            $table->string('rack_no')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add foreign key constraint (inventory_items.id is bigint unsigned)
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            $table->index('inventory_item_id');
            $table->index('product_id');
            $table->index('alert_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_item_variants');
    }
};

