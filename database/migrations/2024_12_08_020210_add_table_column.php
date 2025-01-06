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
        Schema::table('order_quotations', function (Blueprint $table) {
            $table->json('bonus')->nullable()->after('total_amount');
        });
    }

    public function down()
    {
        //
    }
};
