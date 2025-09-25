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

        // // Staging: Done
        // Schema::create('campaigns', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->string('slug')->unique();
        //     $table->text('description')->nullable();
        //     $table->text('internal_description')->nullable();
        //     $table->json('thumbnail')->nullable();
        //     $table->double('base_amount')->default(0);
        //     $table->double('booking_amount')->default(0);
        //     $table->date('start_date')->nullable();
        //     $table->date('end_date')->nullable();
        //     $table->timestamp('published_at')->nullable();
        //     $table->unsignedBigInteger('published_by')->nullable();
        //     $table->integer('slot_total')->default(0);
        //     $table->integer('slot_used')->default(0);
        //     $table->integer('slot_remaining')->default(0);
        //     $table->string('status')->default('unpublished');
        //     $table->json('metadata')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // Schema::create('bookings', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('campaign_id')->nullable();
        //     $table->unsignedBigInteger('campaign_package_id')->nullable();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->string('booking_no');
        //     $table->string('booking_hash')->unique(); // Unique value for URL reference
        //     $table->double('amount')->default(0);
        //     $table->string('payment_url')->nullable();
        //     $table->timestamp('booked_at')->nullable();
        //     $table->timestamp('expired_at')->nullable();
        //     $table->text('internal_remark')->nullable();
        //     $table->string('status')->default('pending');
        //     $table->json('metadata')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
        //     $table->foreign('campaign_package_id')->references('id')->on('campaign_packages')->onDelete('set null');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        // });


        // // Staging: Done
        // Schema::create('campaign_packages', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('campaign_id');
        //     $table->string('name');
        //     $table->text('description')->nullable();
        //     $table->text('internal_description')->nullable();
        //     $table->double('base_amount')->nullable();
        //     $table->double('booking_amount')->nullable();
        //     $table->date('start_date')->nullable();
        //     $table->date('end_date')->nullable();
        //     $table->integer('slot_total')->default(0);
        //     $table->integer('slot_used')->default(0);
        //     $table->integer('slot_remaining')->default(0);
        //     $table->string('status')->default('unpublished');
        //     $table->json('metadata')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        // });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->double('booking_amount')->default(0)->after('base_amount');
        });

        Schema::table('campaign_packages', function (Blueprint $table) {
            $table->double('booking_amount')->nullable()->after('base_amount');
        });
    }

    public function down()
    {
        // 
    }
};
