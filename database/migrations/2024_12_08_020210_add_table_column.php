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
        Schema::table('defect_inspection_forms', function (Blueprint $table) {
            $table->string('link_status')->default('unactive')->after('status'); // Status column
            $table->string('report_hash')->unique()->after('status'); // Column for the Base64url-encoded string
        });
    }

    public function down()
    {
        // 
    }
};
