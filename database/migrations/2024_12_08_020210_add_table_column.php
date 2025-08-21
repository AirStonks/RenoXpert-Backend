<?php

use App\Models\Property;
use App\Models\PropertyROI;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // SAMPLE
        // STAGING: Done
        // Schema::table('users', function (Blueprint $table) {
        //     $table->string('country_code')->nullable()->after('status');
        // });

        Schema::create('reno_x_sale', function (Blueprint $table) {
            $table->id();
            $table->string('reno_sale_no');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('unit_type')->nullable();
            $table->string('block')->nullable();
            $table->string('floor')->nullable();
            $table->string('unit_no')->nullable();
            $table->integer('bedroom_count')->default(0);
            $table->integer('single_bedroom_count')->default(0);
            $table->integer('queen_bedroom_count')->default(0);
            $table->integer('studio_count')->default(0);
            $table->integer('bathroom_count')->default(0);
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('reno_sale_id')->nullable()->after('order_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('reno_sale_id')->nullable()->after('sale_id');
        });

        // Staging: Done
        Schema::table('po_items', function (Blueprint $table) {
            $table->integer('supply_qty')->default(0)->after('supply_price');
            $table->integer('install_qty')->default(0)->after('install_price');
        });
    }

    public function down()
    {
        // 
    }
};
