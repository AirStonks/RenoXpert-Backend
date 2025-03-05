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
        // Schema::create('roles', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('role_name');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // Schema::create('permissions', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('permission_name');
        //     $table->string('permission_description')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // Schema::create('resources', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('resource_name');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // Schema::create('resource_items', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('resource_id')->constrained()->onDelete('cascade');
        //     $table->unsignedBigInteger('item_reference_id');
        //     $table->string('item_reference_type');
        //     $table->string('item_name');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // Schema::create('user_role', function (Blueprint $table) {
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('role_id')->constrained()->onDelete('cascade');
        //     $table->primary(['user_id', 'role_id']);
        //     $table->timestamps();
        // });

        // // Staging: Done
        // Schema::create('user_permission', function (Blueprint $table) {
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('permission_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('resource_id')->constrained()->onDelete('cascade');
        //     $table->unique(['user_id', 'permission_id', 'resource_id']);
        //     $table->timestamps();
        // });

        // // Staging: Done
        // Schema::create('role_permission', function (Blueprint $table) {
        //     $table->foreignId('role_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('permission_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('resource_id')->constrained()->onDelete('cascade');
        //     $table->unique(['role_id', 'permission_id', 'resource_id']);
        //     $table->timestamps();
        // });

        // // Staging: Done
        // Schema::create('user_item_permission', function (Blueprint $table) {
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('permission_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('item_id')->constrained('resource_items')->onDelete('cascade');
        //     $table->unique(['user_id', 'permission_id', 'item_id']);
        //     $table->timestamps();
        // });

        // // Staging: Done
        // Schema::create('role_item_permission', function (Blueprint $table) {
        //     $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
        //     $table->foreignId('permission_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('item_id')->constrained('resource_items')->onDelete('cascade');
        //     $table->unique(['role_id', 'permission_id', 'item_id']);
        //     $table->timestamps();
        // });

        // // Staging: Done
        // Schema::create('po_packages', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('po_id')->constrained('purchase_orders')->onDelete('cascade');
        //     $table->string('name')->nullable();
        //     $table->string('description')->nullable();
        //     $table->string('description_internal')->nullable();
        //     $table->string('category')->nullable();
        //     $table->integer('quantity')->default(1);
        //     $table->double('total_price')->nullable();
        //     $table->string('status')->nullable()->default('pending');
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // // Staging: Done
        // Schema::table('po_items', function (Blueprint $table) {
        //     $table->foreignId('po_package_id')->after('po_id')->constrained()->onDelete('cascade');
        //     $table->double('install_price')->nullable()->after('install');
        //     $table->double('supply_price')->nullable()->after('supply');
        //     $table->string('uom')->nullable()->after('qty');
        //     $table->dropColumn('po_id');
        // });

        // // Staging: Done
        // Schema::table('reno_progress', function (Blueprint $table) {
        //     $table->foreignId('resource_id')->after('contractor_handover_date')->constrained()->onDelete('cascade');
        //     $table->unsignedBigInteger('permission_id')->default(1)->after('resource_id');
        // });
    }

    public function down()
    {
        //
    }
};
