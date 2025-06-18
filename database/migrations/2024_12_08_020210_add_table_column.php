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


        // // Staging: Done
        // Schema::create('reno_sales', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('reno_progress_id');
        //     $table->unsignedBigInteger('sale_id');
        //     $table->boolean('is_main')->default(false);
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->foreign('reno_progress_id')->references('id')->on('reno_progress')->cascadeOnDelete();
        //     $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        // });

        // Staging: Done
        // Schema::create('investor_interests', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('full_name');
        //     $table->string('mobile_number');
        //     $table->string('email');
        //     $table->string('property_name');
        //     $table->string('unit_type');
        //     $table->string('keys_collected');
        //     $table->json('concerns');
        //     $table->json('rental_strategy');
        //     $table->json('support_needed');
        //     $table->string('preferred_contact');
        //     $table->string('preferred_time')->nullable();
        //     $table->string('status')->default('new');
        //     $table->timestamps();
        // });

        // Staging: Done
        Schema::table('reno_progress', function (Blueprint $table) {
            $table->string('rpm_acknowledge_status')->default('pending')->after('sent_to_lark_date');
        });
    }

    public function down()
    {
        // 
    }
};
