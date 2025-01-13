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
            $table->unsignedBigInteger('pm_category_id')->nullable();
            $table->string('SKU')->unique()->nullable();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('uom')->nullable();
            $table->integer('task_weightage')->default(1);
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('depth')->nullable();
            $table->string('internal_desc')->nullable();
            $table->string('status')->nullable()->default('available');
            $table->json('attachments')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pm_category_id')->references('id')->on('pm_categories')->onDelete('set null');
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
