<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 8)->nullable()->after('type');
        });

        // Backfill existing users with a unique code (saveQuietly: skip model events / updated_by).
        User::whereNull('referral_code')->orderBy('id')->each(function ($user) {
            $user->referral_code = User::generateReferralCode();
            $user->saveQuietly();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('referral_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['referral_code']);
            $table->dropColumn('referral_code');
        });
    }
};
