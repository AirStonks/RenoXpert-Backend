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
        // Schema::table('invoices', function (Blueprint $table) {
        //     // Remove the foreign key constraint for sale_id
        //     $table->dropForeign(['sale_id']);

        //     // Rename sale_id column to item_id
        //     $table->renameColumn('sale_id', 'item_id');

        //     // Add item_type column
        //     $table->string('item_type')->after('item_id');
        // });
        
        
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add item_type column
            $table->double('remaining_percentage')->nullable()->after('total_amount');
            $table->double('remaining_amount')->nullable()->after('total_amount');
        });
    }

    public function down()
    {
        // 
    }
};
