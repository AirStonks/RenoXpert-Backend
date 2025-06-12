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


        // Staging: Done
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->integer('studio_count')->nullable()->after('queen_bedroom_count');
        // });


        // Staging: Done
        Schema::create('property_rois', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('thumbnail_title')->nullable();
            $table->string('thumbnail_desc')->nullable();
            $table->json('content')->nullable();
            $table->boolean('view_enabled')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });

        // Staging: Done
        Schema::table('properties', function (Blueprint $table) {
            $table->string('thumbnail_url')->nullable()->after('description');
        });


        // Staging: Done
        // Automatically create property_rois records for existing properties
        $properties = Property::all();
        foreach ($properties as $property) {
            PropertyROI::create([
                'property_id' => $property->id,
                'thumbnail_title' => '', // Customize as needed
                'thumbnail_desc' => '', // Customize as needed
                'content' => [
                    'features' => [],
                    'gallery' => []
                ], // Default JSON content
                'view_enabled' => true,
                'created_by' => null, // Set to a default user ID or null
                'updated_by' => null, // Set to a default user ID or null
            ]);
        }
    }

    public function down()
    {
        // 
    }
};
