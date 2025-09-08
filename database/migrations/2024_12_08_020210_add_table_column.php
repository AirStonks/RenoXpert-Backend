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

        // // Staging: Done
        // // POSTPONDED
        // Schema::table('quotation_packages', function (Blueprint $table) {
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // // POSTPONDED
        // Schema::table('quo_pkg_prods', function (Blueprint $table) {
        //     $table->softDeletes();
        // });

        // Staging: Done
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_rnpl')->default(false)->after('is_progressive_payment');
            $table->double('rnpl_base_price')->default(0)->after('is_rnpl');
        });

        // // Staging: Done
        // Schema::create('reno_x_sale', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('reno_sale_no');
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->unsignedBigInteger('property_id')->nullable();
        //     $table->string('unit_type')->nullable();
        //     $table->string('block')->nullable();
        //     $table->string('floor')->nullable();
        //     $table->string('unit_no')->nullable();
        //     $table->integer('bedroom_count')->default(0);
        //     $table->integer('single_bedroom_count')->default(0);
        //     $table->integer('queen_bedroom_count')->default(0);
        //     $table->integer('studio_count')->default(0);
        //     $table->integer('bathroom_count')->default(0);
        //     $table->string('status')->default('active');
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // Schema::table('sales', function (Blueprint $table) {
        //     $table->unsignedBigInteger('reno_sale_id')->nullable()->after('order_id');
        // });

        // // Staging: Done
        // Schema::table('purchase_orders', function (Blueprint $table) {
        //     $table->unsignedBigInteger('reno_sale_id')->nullable()->after('sale_id');
        // });

        // // Staging: Done
        // Schema::table('po_items', function (Blueprint $table) {
        //     $table->integer('supply_qty')->default(0)->after('supply_price');
        //     $table->integer('install_qty')->default(0)->after('install_price');
        // });

        // // Staging: Done
        // Schema::create('api_keys', function (Blueprint $table) {
        //     $table->id();
        //     $table->uuid('uuid')->unique();
        //     $table->string('name');
        //     $table->text('key')->unique();
        //     $table->string('prefix', 8);
        //     $table->timestamp('last_used_at')->nullable();
        //     $table->timestamp('expires_at')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        //     $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        //     $table->index(['prefix', 'is_active']);
        // });

        // Staging: Done
        Schema::table('reno_progress', function (Blueprint $table) {
            $table->timestamp('owner_handover_released_at')->nullable()->after('completed_at');
            $table->timestamp('owner_handover_submitted_at')->nullable()->after('owner_handover_released_at');
        });
    }

    public function down()
    {
        // 
    }
};
