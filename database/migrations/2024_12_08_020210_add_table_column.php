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
