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

        // // Staging: Done
        // Schema::table('po_packages', function (Blueprint $table) {
        //     $table->unsignedBigInteger('package_id')->nullable()->after('po_id');
        // });

        Schema::table('po_packages', function (Blueprint $table) {
            $table->unsignedInteger('sequence')->default(0)->after('id');
        });

        Schema::table('po_items', function (Blueprint $table) {
            $table->unsignedInteger('sequence')->default(0)->after('id');
        });
    }

    public function down()
    {
        //
    }
};
