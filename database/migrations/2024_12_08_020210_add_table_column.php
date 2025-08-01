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
        // Staging: Done
        // PRD: Done
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
        Schema::table('packages', function (Blueprint $table) {
            $table->double('monthly_amount')->default(0)->after('total_price');
            $table->integer('tenure')->default(0)->after('total_price');
            $table->double('markup_percentage')->default(0)->after('total_price');
            $table->double('markup_amount')->default(0)->after('total_price');
        });

        // Staging: Done
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('tenure')->default(0)->after('completion_day');
            $table->double('be_powered_base_price')->default(0)->after('is_be_powered');
            $table->double('installment_amount')->default(0)->after('is_be_powered');
            $table->string('installment_method')->default('dynamic')->after('is_be_powered');
        });
    }

    public function down()
    {
        // 
    }
};
