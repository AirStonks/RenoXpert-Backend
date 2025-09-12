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
