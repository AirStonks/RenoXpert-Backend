<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('referred_by_user_id')->nullable()->after('user_id');
            $table->string('referral_code')->nullable()->after('referred_by_user_id');
            $table->index('referred_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['referred_by_user_id']);
            $table->dropColumn(['referred_by_user_id', 'referral_code']);
        });
    }
};
