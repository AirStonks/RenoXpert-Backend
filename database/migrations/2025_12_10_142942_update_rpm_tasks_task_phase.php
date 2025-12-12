<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define phase mappings
        $p1Items = [
            'Wiring',
            'Lighting',
            'LED Track Lighting',
            'Fan',
            'Painting',
            'Painting & Featured Wall',
            'Water Heater',
            'Smart Main Door Lock',
            'G2 Gateway Hub',
            'SMART METER',
            'SMART LOCK (Room)',
            'Partition Wall',
        ];

        $p2aItems = [
            'Bedframe',
            'Wardrobe',
            'Table',
            'Chair',
            'Curtain',
            'Wall Mirror',
            'Dining Table',
            'Dining Chair',
            'Shoe Cabinet',
            'CCTV & Shelve',
            'Kitchen Cabinet Base Unit',
            'Kitchen Top',
            'Wall Unit',
            'Kitchen Sink',
            'Sofa',
            'TV Console',
            'Coffee Table',
        ];

        $p2bItems = [
            'Mattress',
            'Matterss Protector',
            'Portrait',
            'Door Stopper',
            'Mini Fridge',
            'Air Cond',
            'Cloth Hanger',
            'Bidet',
            'Cloth Drying Rack',
            'Doorbell',
            'Fire Extinguisher',
            'Cleaning Tools Set',
            'Hood',
            'Water Dispenser',
            'Microwave',
            'Induction Cooker',
            'Washer',
            'Dryer',
        ];

        // Update p1 items
        DB::table('rpm_tasks')
            ->whereIn('item_name', $p1Items)
            ->update(['task_phase' => 'p1']);

        // Update p2a items
        DB::table('rpm_tasks')
            ->whereIn('item_name', $p2aItems)
            ->update(['task_phase' => 'p2a']);

        // Update p2b items
        DB::table('rpm_tasks')
            ->whereIn('item_name', $p2bItems)
            ->update(['task_phase' => 'p2b']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set task_phase to null for all records that were updated
        $p1Items = [
            'Wiring',
            'Lighting',
            'LED Track Lighting',
            'Fan',
            'Painting',
            'Painting & Featured Wall',
            'Water Heater',
            'Smart Main Door Lock',
            'G2 Gateway Hub',
            'SMART METER',
            'SMART LOCK (Room)',
            'Partition Wall',
        ];

        $p2aItems = [
            'Bedframe',
            'Wardrobe',
            'Table',
            'Chair',
            'Curtain',
            'Wall Mirror',
            'Dining Table',
            'Dining Chair',
            'Shoe Cabinet',
            'CCTV & Shelve',
            'Kitchen Cabinet Base Unit',
            'Kitchen Top',
            'Wall Unit',
            'Kitchen Sink',
            'Sofa',
            'TV Console',
            'Coffee Table',
        ];

        $p2bItems = [
            'Mattress',
            'Matterss Protector',
            'Portrait',
            'Door Stopper',
            'Mini Fridge',
            'Air Cond',
            'Cloth Hanger',
            'Bidet',
            'Cloth Drying Rack',
            'Doorbell',
            'Fire Extinguisher',
            'Cleaning Tools Set',
            'Hood',
            'Water Dispenser',
            'Microwave',
            'Induction Cooker',
            'Washer',
            'Dryer',
        ];

        $allItems = array_merge($p1Items, $p2aItems, $p2bItems);

        DB::table('rpm_tasks')
            ->whereIn('item_name', $allItems)
            ->update(['task_phase' => null]);
    }
};
