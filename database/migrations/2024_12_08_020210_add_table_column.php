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
        // Staging: done
        // Schema::table('products', function (Blueprint $table) {
        //     $table->text('internal_desc')->nullable()->change();
        // });

        Schema::table('payments', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('currency');
            $table->string('remark')->nullable()->after('currency');
            $table->string('receiving_account')->nullable()->after('currency');
            $table->string('bank')->nullable()->after('currency');
            $table->string('payment_date')->nullable()->after('currency');
            $table->string('payment_channel')->nullable()->after('currency');
            $table->string('payment_method')->nullable()->after('currency');
        });
    }

    public function down()
    {
        //
    }
};
