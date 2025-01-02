<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Schema::table('quotations', function (Blueprint $table) {
        //     $table->boolean('is_ready')->default(false)->after('description_internal');
        //     $table->unsignedBigInteger('property_id')->nullable()->after('name');

        //     $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
        // });

        // Schema::table('products', function (Blueprint $table) {
            // $table->timestamp('archived_at')->nullable()->after('updated_by');
            // $table->unsignedBigInteger('archived_by')->nullable()->after('updated_by');
            // $table->string('status')->nullable()->default('available')->change();
        // });
        
        // Schema::table('packages', function (Blueprint $table) {
        //     $table->timestamp('archived_at')->nullable()->after('updated_by');
        //     $table->unsignedBigInteger('archived_by')->nullable()->after('updated_by');
        //     $table->string('status')->nullable()->default('available')->after('total_price');
        // });
        
        // Schema::table('quotations', function (Blueprint $table) {
        //     $table->timestamp('archived_at')->nullable()->after('updated_by');
        //     $table->unsignedBigInteger('archived_by')->nullable()->after('updated_by');
        //     $table->string('status')->nullable()->default('available')->after('metadata');
        // });
    }

    public function down()
    {
        //
    }
};
