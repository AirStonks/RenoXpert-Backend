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
        Schema::create('key_management', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reno_progress_id')->nullable();
            $table->timestamp('date_received_key')->nullable();
            $table->timestamp('date_posted')->nullable();
            $table->string('pic_name')->nullable();
            $table->integer('no_main_door')->default(0);
            $table->integer('no_room')->default(0);
            $table->string('status')->default('not_started');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reno_progress_id')->references('id')->on('reno_progress')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_management');
    }
};
