<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Property;
use App\Models\Quotation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'name_first' => 'Super',
            'name_last' => 'Admin',
            'email' => 'dev@gmail.com',
            'password' => 'developer',
            'phone_no' => '0123456789',
            'type' => 'super-admin',
        ]);

        // ProductCategory::create([
        //     'name' => 'Bedroom',
        //     'description' => 'This is Bedroom',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Kitchen',
        //     'description' => 'This is Bedroom',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Dining, Yard & Foyer',
        //     'description' => 'This is Dining, Yard & Foyer',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Commune Living Space',
        //     'description' => 'This is Commune Living Space',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Toilet Furnishing (Freebies)',
        //     'description' => 'This is Toilet Furnishing',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Electrical Appliances',
        //     'description' => 'This is Electrical Appliances',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Air Conditioning & Piping Works',
        //     'description' => 'This is Air Conditioning & Piping Works',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Smart IoT Devices for Co-Living Solutions',
        //     'description' => 'This is Smart IoT Devices for Co-Living Solutions',
        // ]);

        ProductCategory::create([
            'name' => 'Others',
            'description' => 'Other category products goes this category',
        ]);

        Product::create([
            'id' => 1,
            'name' => 'Accent Wall - Designer-look painting',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 300.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 2,
            'name' => 'Built-In Queen-sized Bedhead & Bedframe',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 550.00,
            'product_cost_of_good_sold' => 390.00,
            'product_excluded_price' => 460.00,
            'description' => 'with 2nos Soft-Close System Drawers, Fabricated w/ LED strip & 13A plugpoint',
        ]);

        Product::create([
            'id' => 3,
            'name' => 'Built-In 3 Doors Swing Wardrobe',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 700.00,
            'product_cost_of_good_sold' => 415.00,
            'product_excluded_price' => 610.00,
            'description' => 'with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w/ LED strip & 2nos 13A plugpoints',
        ]);

        Product::create([
            'id' => 4,
            'name' => 'Built-In Study Table Set',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 300.00,
            'product_cost_of_good_sold' => 230.00,
            'product_excluded_price' => 250.00,
            'description' => '750mm (W) x 750mm (H) x 480mm (D) Fabricated w/ 13A plugpoints',
        ]);

        Product::create([
            'id' => 5,
            'name' => 'Built-In Wall-Mounted Cabinet Unit',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 130.00,
            'description' => 'Fabricated w/ LED Strip',
        ]);

        Product::create([
            'id' => 6,
            'name' => 'Goodnite Branded - 10" Queen-sized mattress with 10 years warranty',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 800.00,
            'product_cost_of_good_sold' => 500.00,
            'product_excluded_price' => 600.00,
            'description' => 'Bathroom Wall Mirror',
        ]);

        Product::create([
            'id' => 7,
            'name' => 'Protector, Pillow, Queen-sized bedsheet set with comforter',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 300.00,
            'product_cost_of_good_sold' => 100.00,
            'product_excluded_price' => 200.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 8,
            'name' => 'Optimal-Designed Writing Chair',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 150.00,
            'product_cost_of_good_sold' => 100.00,
            'product_excluded_price' => 100.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 9,
            'name' => 'Semi blackout full length curtain',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 500.00,
            'product_cost_of_good_sold' => 400.00,
            'product_excluded_price' => 400.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 10,
            'name' => 'Soft LED lighting',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 180.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 0.00,
            'description' => '(2 downlights & 1 track light)',
        ]);

        Product::create([
            'id' => 11,
            'name' => 'Supply and install a branded ceiling fan',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 180.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 12,
            'name' => 'Designer-Approved Decorative set',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 150.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 13,
            'name' => '9.5mm drywall partition, with skim, paint, knobs, hinges, and wooden door',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 2000.00,
            'product_cost_of_good_sold' => 1600.00,
            'product_excluded_price' => 1800.00,
            'description' => ' (< 150sqft)',
        ]);

        // ---------------------------------------------------

        Product::create([
            'id' => 14,
            'name' => 'Accent Wall - Designer-look painting',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 300.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 15,
            'name' => 'Built-In Single-sized Bedhead & Bedframe',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 400.00,
            'product_cost_of_good_sold' => 280.00,
            'product_excluded_price' => 380.00,
            'description' => 'with 2nos Soft-Close System Drawers, Fabricated w/ LED strip & 13A plugpoint',
        ]);

        Product::create([
            'id' => 16,
            'name' => 'Built-In 2 Doors Swing Wardrobe',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 500.00,
            'product_cost_of_good_sold' => 290.00,
            'product_excluded_price' => 480.00,
            'description' => 'with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w/ LED strip & 2nos 13A plugpoints',
        ]);

        Product::create([
            'id' => 17,
            'name' => 'Built-In Study Table Set',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 300.00,
            'product_cost_of_good_sold' => 230.00,
            'product_excluded_price' => 250.00,
            'description' => '750mm (W) x 750mm (H) x 480mm (D) Fabricated w/ 13A plugpoints',
        ]);

        Product::create([
            'id' => 18,
            'name' => 'Built-In Wall-Mounted Cabinet Unit',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 130.00,
            'description' => 'Fabricated w/ LED Strip',
        ]);

        Product::create([
            'id' => 19,
            'name' => 'Goodnite Branded - 10" Single-sized mattress with 10 years warranty',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 800.00,
            'product_cost_of_good_sold' => 350.00,
            'product_excluded_price' => 638.00,
            'description' => 'Damask Fabric w/ Posture Spring System, Non-Flip Tech',
        ]);

        Product::create([
            'id' => 20,
            'name' => 'Protector, Pillow, Single-sized bedsheet set with comforter',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 180.00,
            'product_cost_of_good_sold' => 100.00,
            'product_excluded_price' => 150.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 21,
            'name' => 'Optimal-Designed Writing Chair',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 150.00,
            'product_cost_of_good_sold' => 100.00,
            'product_excluded_price' => 100.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 22,
            'name' => 'Semi blackout full length curtain',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 500.00,
            'product_cost_of_good_sold' => 400.00,
            'product_excluded_price' => 400.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 23,
            'name' => 'Soft LED lighting',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 180.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 0.00,
            'description' => '(2 downlights & 1 track light)',
        ]);

        Product::create([
            'id' => 24,
            'name' => 'Supply and install a branded ceiling fan',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 180.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 25,
            'name' => 'Designer-Approved Decorative set',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 150.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 26,
            'name' => '9.5mm drywall partition, with skim, paint, knobs, hinges, and wooden door',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 1800.00,
            'product_cost_of_good_sold' => 1600.00,
            'product_excluded_price' => 1600.00,
            'description' => ' (< 150sqft)',
        ]);

        // ===================================================================
        // ===================================================================
        // ===================================================================

        Product::create([
            'id' => 27,
            'name' => 'Soft LED lighting (2 lights / track lights) & required wiring works',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 480.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 150.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 28,
            'name' => '5ft - 7ft Built-In Kitchen Cabinet Package with LED Ambient Strip',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 5000.00,
            'product_cost_of_good_sold' => 2600.00,
            'product_excluded_price' => 3000.00,
            'description' => '',
        ]);

        // ===================================================================
        // ===================================================================
        // ===================================================================

        Product::create([
            'id' => 29,
            'name' => 'Dining bar table',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 500.00,
            'product_cost_of_good_sold' => 480.00,
            'product_excluded_price' => 480.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 30,
            'name' => 'Dining chairs',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 120.00,
            'product_cost_of_good_sold' => 100.00,
            'product_excluded_price' => 100.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 31,
            'name' => 'Built-In Shoe Cabinet (W:900mm x H:1200mm x D:350mm)',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 430.00,
            'product_cost_of_good_sold' => 400.00,
            'product_excluded_price' => 400.00,
            'description' => ' with Bench (W:500mm x H:450mm x D:350mm)',
        ]);

        Product::create([
            'id' => 32,
            'name' => 'Supply and install cloth hanger',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 180.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 150.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 33,
            'name' => 'Fire extinguishers (Dining)',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 80.00,
            'product_cost_of_good_sold' => 50.00,
            'product_excluded_price' => 50.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 34,
            'name' => 'Soft LED lighting (Dining)',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 250.00,
            'product_cost_of_good_sold' => 210.00,
            'product_excluded_price' => 210.00,
            'description' => '(Downlights & Pendant Light)',
        ]);

        Product::create([
            'id' => 35,
            'name' => 'Additional wiring-related work for plugs for Wifi & CCTV',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 30.00,
            'product_cost_of_good_sold' => 25.00,
            'product_excluded_price' => 25.00,
            'description' => '',
        ]);

        // ===================================================================
        // ===================================================================
        // ===================================================================

        Product::create([
            'id' => 36,
            'name' => 'Fire extinguishers (Commune Living Space)',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 250.00,
            'product_cost_of_good_sold' => 200.00,
            'product_excluded_price' => 200.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 37,
            'name' => 'Soft LED lighting (Commune Living Space)',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 220.00,
            'product_cost_of_good_sold' => 200.00,
            'product_excluded_price' => 200.00,
            'description' => '(Downlights & Pendant Light)',
        ]);

        Product::create([
            'id' => 38,
            'name' => 'Supply and install branded ceiling fan',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 180.00,
            'product_excluded_price' => 180.00,
            'description' => '(Living Space & Dining Space)',
        ]);

        Product::create([
            'id' => 40,
            'name' => 'Curtain (semi blackout full length) with track',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 550.00,
            'product_cost_of_good_sold' => 500.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);
        
        Product::create([
            'id' => 41,
            'name' => 'Tatami Living Platform ',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 800.00,
            'product_cost_of_good_sold' => 780.00,
            'product_excluded_price' => 0.00,
            'description' => '(W: 910mm x L: 1900mm x H: 300mm)',
        ]);
        
        Product::create([
            'id' => 42,
            'name' => 'Convertible Tatami Bench',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 750.00,
            'product_cost_of_good_sold' => 730.00,
            'product_excluded_price' => 0.00,
            'description' => '(W: 2400mm x H: 450mm x D: 400mm)',
        ]);
        
        Product::create([
            'id' => 43,
            'name' => 'Coffee Table',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 750.00,
            'product_cost_of_good_sold' => 250.00,
            'product_excluded_price' => 0.00,
            'description' => '(W: 2400mm x H: 450mm x D: 400mm)',
        ]);

        // ===================================================================
        // ===================================================================
        // ===================================================================

        Product::create([
            'id' => 44,
            'name' => 'Supply and install downlight',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 0.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 45,
            'name' => 'Supply and install on wall mirror',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 0.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 46,
            'name' => 'Supply and install water heater',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 0.00,
            'product_cost_of_good_sold' => 180.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 47,
            'name' => 'Supply and install on clothes hanger',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 0.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        // ===================================================================
        // ===================================================================
        // ===================================================================

        Product::create([
            'id' => 48,
            'name' => 'Supply & install 8kg washer front load with IoT Enabled',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 1500.00,
            'product_cost_of_good_sold' => 1100.00,
            'product_excluded_price' => 1100.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 49,
            'name' => 'Supply & install 8kg dryer front load with IoT Enabled',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 1500.00,
            'product_cost_of_good_sold' => 1100.00,
            'product_excluded_price' => 1100.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 50,
            'name' => 'Supply & install Combo 2 In 1 Washer Dryer with IoT Enabled',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 0.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 51,
            'name' => 'Supply and Install hood and hob',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 2000.00,
            'product_cost_of_good_sold' => 1500.00,
            'product_excluded_price' => 1500.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 52,
            'name' => 'Supply & Install iBilikPlus IoT Enabled Smart Main Door Lock',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 545.00,
            'product_cost_of_good_sold' => 600.00,
            'product_excluded_price' => 600.00,
            'description' => 'with double latches',
        ]);

        Product::create([
            'id' => 53,
            'name' => 'Supply and install CCTV in dining area',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 250.00,
            'product_cost_of_good_sold' => 180.00,
            'product_excluded_price' => 180.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 54,
            'name' => 'Microwave',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 400.00,
            'product_cost_of_good_sold' => 230.00,
            'product_excluded_price' => 230.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 55,
            'name' => 'Hot & Warm Water Dispenser c/w 4 Layer Korea Technology Filtration',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 380.00,
            'product_cost_of_good_sold' => 266.00,
            'product_excluded_price' => 266.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 56,
            'name' => '2 door mini bar Fridge',
            'category_id' => 1,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 500.00,
            'product_cost_of_good_sold' => 400.00,
            'product_excluded_price' => 400.00,
            'description' => '',
        ]);

        // ===================================================================
        // ===================================================================
        // ===================================================================

        Product::create([
            'id' => 57,
            'name' => 'Supply and install 1 hp aircond without copper piping - midea/ gree/ hisense',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 1300.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 58,
            'name' => 'Relocation of aircond to the partitioned room',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 250.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        // ===================================================================
        // ===================================================================
        // ===================================================================

        Product::create([
            'id' => 59,
            'name' => 'Supply & Install iBilikPlus IoT Enabled Smart Room Door Lock',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 350.00,
            'product_cost_of_good_sold' => 297.00,
            'product_excluded_price' => 297.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 60,
            'name' => 'Supply & Install iBilikPlus IoT Enabled Smart Meter connected to WHOLE room',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 500.00,
            'product_cost_of_good_sold' => 199.00,
            'product_excluded_price' => 199.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 61,
            'name' => 'Supply & Install Smart WIFI G2 Gateway Hub',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 168.00,
            'product_cost_of_good_sold' => 168.00,
            'product_excluded_price' => 168.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 62,
            'name' => 'Manpower cost for M&E AND Painting',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 700.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 63,
            'name' => 'Roudup for Partition Queen-Sized Bedroom',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 920.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 64,
            'name' => 'Roundup for Partition Single-Sized Bedroom',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 1090.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 65,
            'name' => 'Roundup for Queen-Sized Bedroom',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 920.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 66,
            'name' => 'Roundup for Single-Sized Bedroom',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 890.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 67,
            'name' => 'Roundup for Kitchen',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 320.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 68,
            'name' => 'Roundup for Dining, Yard & Foyer',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 1610.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'id' => 69,
            'name' => 'Roundup for Commune Living Space',
            'category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'product_retail_price' => 3250.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        $package1 = Package::create([
            'name' => 'Partition Queen-Sized Bedroom',
            'description' => 'This is the Partition Queen-Sized Bedroom',
            'total_price' => 8000.00
        ]);

        $package1->products()->attach(1, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(2, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(3, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(4, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(5, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(6, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(7, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(8, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(9, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(10, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(11, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(12, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(13, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(62, ['quantity' => 1, 'visibility' => 1]);
        $package1->products()->attach(63, ['quantity' => 1, 'visibility' => 0]);

        $package2 = Package::create([
            'name' => 'Partition Single-Sized Bedroom',
            'description' => 'This is the Partition Single-Sized Bedroom',
            'total_price' => 7500.00
        ]);

        $package2->products()->attach(14, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(15, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(16, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(17, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(18, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(19, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(20, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(21, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(22, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(23, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(24, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(25, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(26, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(62, ['quantity' => 1, 'visibility' => 1]);
        $package2->products()->attach(64, ['quantity' => 1, 'visibility' => 0]);

        $package3 = Package::create([
            'name' => 'Queen-Sized Bedroom',
            'description' => 'This is the Queen-Sized Bedroom',
            'total_price' => 7500.00
        ]);

        $package3->products()->attach(1, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(2, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(3, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(4, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(5, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(6, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(7, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(8, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(9, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(10, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(11, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(12, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(62, ['quantity' => 1, 'visibility' => 1]);
        $package3->products()->attach(65, ['quantity' => 1, 'visibility' => 0]);

        $package4 = Package::create([
            'name' => 'Single-Sized Bedroom',
            'description' => 'This is the Single-Sized Bedroom',
            'total_price' => 5500.00
        ]);

        $package4->products()->attach(14, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(15, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(16, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(17, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(18, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(19, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(20, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(21, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(22, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(23, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(24, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(25, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(62, ['quantity' => 1, 'visibility' => 1]);
        $package4->products()->attach(66, ['quantity' => 1, 'visibility' => 0]);

        $package5 = Package::create([
            'name' => 'Kitchen',
            'description' => 'This is the Kitchen',
            'total_price' => 6000.00
        ]);

        $package5->products()->attach(27, ['quantity' => 1, 'visibility' => 1]);
        $package5->products()->attach(28, ['quantity' => 1, 'visibility' => 1]);
        $package5->products()->attach(62, ['quantity' => 1, 'visibility' => 1]);
        $package5->products()->attach(67, ['quantity' => 1, 'visibility' => 0]);

        $package6 = Package::create([
            'name' => 'Dining, Yard & Foyer',
            'description' => 'This is the Dining, Yard & Foyer',
            'total_price' => 6000.00
        ]);

        $package6->products()->attach(29, ['quantity' => 1, 'visibility' => 1]);
        $package6->products()->attach(30, ['quantity' => 4, 'visibility' => 1]);
        $package6->products()->attach(31, ['quantity' => 1, 'visibility' => 1]);
        $package6->products()->attach(32, ['quantity' => 1, 'visibility' => 1]);
        $package6->products()->attach(33, ['quantity' => 1, 'visibility' => 1]);
        $package6->products()->attach(34, ['quantity' => 1, 'visibility' => 1]);
        $package6->products()->attach(35, ['quantity' => 4, 'visibility' => 1]);
        $package6->products()->attach(36, ['quantity' => 1, 'visibility' => 1]);
        $package6->products()->attach(62, ['quantity' => 1, 'visibility' => 1]);
        $package6->products()->attach(68, ['quantity' => 1, 'visibility' => 0]);

        $package7 = Package::create([
            'name' => 'Commune Living Space',
            'description' => 'This is the Commune Living Space',
            'total_price' => 9800.00
        ]);

        $package7->products()->attach(29, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(30, ['quantity' => 4, 'visibility' => 1]);
        $package7->products()->attach(31, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(32, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(36, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(37, ['quantity' => 2, 'visibility' => 1]);
        $package7->products()->attach(35, ['quantity' => 4, 'visibility' => 1]);
        $package7->products()->attach(25, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(38, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(41, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(40, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(42, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(62, ['quantity' => 1, 'visibility' => 1]);
        $package7->products()->attach(69, ['quantity' => 1, 'visibility' => 0]);

        $package8 = Package::create([
            'name' => 'Toilet Furnishing (Freebies)',
            'description' => 'This is the Toilet Furnishing (Freebies)',
            'total_price' => 0.00
        ]);

        $package8->products()->attach(44, ['quantity' => 2, 'visibility' => 1]);
        $package8->products()->attach(45, ['quantity' => 1, 'visibility' => 1]);
        $package8->products()->attach(46, ['quantity' => 1, 'visibility' => 1]);
        $package8->products()->attach(47, ['quantity' => 1, 'visibility' => 1]);

        $package9 = Package::create([
            'name' => 'Electrical Appliances Bundle set',
            'description' => 'This is the Electrical Appliances Bundle set',
            'total_price' => 8575.00
        ]);

        $package9->products()->attach(48, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(49, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(50, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(51, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(52, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(53, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(54, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(55, ['quantity' => 1, 'visibility' => 1]);
        $package9->products()->attach(56, ['quantity' => 4, 'visibility' => 1]);

        $package10 = Package::create([
            'name' => 'Air Conditioning & Piping Works',
            'description' => 'This is the Air Conditioning & Piping Works',
            'total_price' => 5450.00
        ]);

        $package10->products()->attach(57, ['quantity' => 4, 'visibility' => 1]);
        $package10->products()->attach(58, ['quantity' => 1, 'visibility' => 1]);

        $package11 = Package::create([
            'name' => 'IoT for Bedroom Bundle set',
            'description' => 'This is the IoT for Bedroom Bundle set',
            'total_price' => 3568.00
        ]);

        $package11->products()->attach(59, ['quantity' => 4, 'visibility' => 1]);
        $package11->products()->attach(60, ['quantity' => 4, 'visibility' => 1]);
        $package11->products()->attach(61, ['quantity' => 1, 'visibility' => 1]);

        // Contact::create([
        //     'name' => 'CK Chang',
        //     'email' => 'ckchang@gmail.com',
        //     'phone_no' => '01136647745',
        //     'race' => 'Chinese',
        //     'gender' => 'Male',
        //     'nationality' => 'Malaysian',
        //     'description' => 'some desc',
        // ]);

        // Contact::create([
        //     'name' => 'Shelyn Ooi',
        //     'email' => 'shelynooi@gmail.com',
        //     'phone_no' => '01111476550',
        //     'race' => 'Chinese',
        //     'gender' => 'Female',
        //     'nationality' => 'Malaysian',
        //     'description' => 'some desc',
        // ]);

        // Contact::create([
        //     'name' => 'Lee',
        //     'email' => 'lee@gmail.com',
        //     'phone_no' => '01118882881',
        //     'race' => 'Chinese',
        //     'gender' => 'Male',
        //     'nationality' => 'Malaysian',
        //     'description' => 'some desc',
        // ]);

        Property::create([
            'name' => 'Meta City',
            'address' => '',
            'street' => 'Jln Atmosphere Utama 2',
            'postcode' => '43400',
            'city' => 'Seri Kembangan',
            'state' => 'Selangor',
            'description' => 'some desc',
        ]);

        Quotation::create([
            'name' => 'MC-TE01',
            'description' => 'Meta City Type E',
            'total_amount' => 64543.00,
            'metadata' => '[{"id":1,"name":"Partition Queen-Sized Bedroom","description":"This is the Partition Queen-Sized Bedroom","total_price":8000,"products":[{"id":1,"name":"Accent Wall - Designer-look painting","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":300,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":1,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":2,"name":"Built-In Queen-sized Bedhead & Bedframe","category_id":1,"SKU":null,"type":"component","description":"with 2nos Soft-Close System Drawers, Fabricated w\/ LED strip & 13A plugpoint","product_retail_price":550,"product_cost_of_good_sold":390,"product_excluded_price":460,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":2,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":3,"name":"Built-In 3 Doors Swing Wardrobe","category_id":1,"SKU":null,"type":"component","description":"with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w\/ LED strip & 2nos 13A plugpoints","product_retail_price":700,"product_cost_of_good_sold":415,"product_excluded_price":610,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":3,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":4,"name":"Built-In Study Table Set","category_id":1,"SKU":null,"type":"component","description":"750mm (W) x 750mm (H) x 480mm (D) Fabricated w\/ 13A plugpoints","product_retail_price":300,"product_cost_of_good_sold":230,"product_excluded_price":250,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":4,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":5,"name":"Built-In Wall-Mounted Cabinet Unit","category_id":1,"SKU":null,"type":"component","description":"Fabricated w\/ LED Strip","product_retail_price":200,"product_cost_of_good_sold":0,"product_excluded_price":130,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":5,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":6,"name":"Goodnite Branded - 10\" Queen-sized mattress with 10 years warranty","category_id":1,"SKU":null,"type":"component","description":"Bathroom Wall Mirror","product_retail_price":800,"product_cost_of_good_sold":500,"product_excluded_price":600,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":6,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":7,"name":"Protector, Pillow, Queen-sized bedsheet set with comforter","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":300,"product_cost_of_good_sold":100,"product_excluded_price":200,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":7,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":8,"name":"Optimal-Designed Writing Chair","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":150,"product_cost_of_good_sold":100,"product_excluded_price":100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":8,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":9,"name":"Semi blackout full length curtain","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":500,"product_cost_of_good_sold":400,"product_excluded_price":400,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":9,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":10,"name":"Soft LED lighting","category_id":1,"SKU":null,"type":"component","description":"(2 downlights & 1 track light)","product_retail_price":180,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":10,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":11,"name":"Supply and install a branded ceiling fan","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":180,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":11,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":12,"name":"Designer-Approved Decorative set","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":150,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":12,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":13,"name":"9.5mm drywall partition, with skim, paint, knobs, hinges, and wooden door","category_id":1,"SKU":null,"type":"component","description":"(< 150sqft)","product_retail_price":2000,"product_cost_of_good_sold":1600,"product_excluded_price":1800,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":13,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":62,"name":"Manpower cost for M&E AND Painting","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":700,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":62,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":63,"name":"Roudup for Partition Queen-Sized Bedroom","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":920,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":1,"product_id":63,"quantity":1,"visibility":0,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":2,"name":"Partition Single-Sized Bedroom","description":"This is the Partition Single-Sized Bedroom","total_price":7500,"products":[{"id":14,"name":"Accent Wall - Designer-look painting","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":300,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":14,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":15,"name":"Built-In Single-sized Bedhead & Bedframe","category_id":1,"SKU":null,"type":"component","description":"with 2nos Soft-Close System Drawers, Fabricated w\/ LED strip & 13A plugpoint","product_retail_price":400,"product_cost_of_good_sold":280,"product_excluded_price":380,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":15,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":16,"name":"Built-In 2 Doors Swing Wardrobe","category_id":1,"SKU":null,"type":"component","description":"with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w\/ LED strip & 2nos 13A plugpoints","product_retail_price":500,"product_cost_of_good_sold":290,"product_excluded_price":480,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":16,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":17,"name":"Built-In Study Table Set","category_id":1,"SKU":null,"type":"component","description":"750mm (W) x 750mm (H) x 480mm (D) Fabricated w\/ 13A plugpoints","product_retail_price":300,"product_cost_of_good_sold":230,"product_excluded_price":250,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":17,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":18,"name":"Built-In Wall-Mounted Cabinet Unit","category_id":1,"SKU":null,"type":"component","description":"Fabricated w\/ LED Strip","product_retail_price":200,"product_cost_of_good_sold":0,"product_excluded_price":130,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":18,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":19,"name":"Goodnite Branded - 10\" Single-sized mattress with 10 years warranty","category_id":1,"SKU":null,"type":"component","description":"Damask Fabric w\/ Posture Spring System, Non-Flip Tech","product_retail_price":800,"product_cost_of_good_sold":350,"product_excluded_price":638,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":19,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":20,"name":"Protector, Pillow, Single-sized bedsheet set with comforter","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":180,"product_cost_of_good_sold":100,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":20,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":21,"name":"Optimal-Designed Writing Chair","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":150,"product_cost_of_good_sold":100,"product_excluded_price":100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":21,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":22,"name":"Semi blackout full length curtain","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":500,"product_cost_of_good_sold":400,"product_excluded_price":400,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":22,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":23,"name":"Soft LED lighting","category_id":1,"SKU":null,"type":"component","description":"(2 downlights & 1 track light)","product_retail_price":180,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":23,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":24,"name":"Supply and install a branded ceiling fan","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":180,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":24,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":25,"name":"Designer-Approved Decorative set","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":150,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":25,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":26,"name":"9.5mm drywall partition, with skim, paint, knobs, hinges, and wooden door","category_id":1,"SKU":null,"type":"component","description":"(< 150sqft)","product_retail_price":1800,"product_cost_of_good_sold":1600,"product_excluded_price":1600,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":26,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":62,"name":"Manpower cost for M&E AND Painting","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":700,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":62,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":64,"name":"Roundup for Partition Single-Sized Bedroom","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":1090,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":2,"product_id":64,"quantity":1,"visibility":0,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":3,"name":"Queen-Sized Bedroom","description":"This is the Queen-Sized Bedroom","total_price":6000,"products":[{"id":1,"name":"Accent Wall - Designer-look painting","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":300,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":1,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":2,"name":"Built-In Queen-sized Bedhead & Bedframe","category_id":1,"SKU":null,"type":"component","description":"with 2nos Soft-Close System Drawers, Fabricated w\/ LED strip & 13A plugpoint","product_retail_price":550,"product_cost_of_good_sold":390,"product_excluded_price":460,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":2,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":3,"name":"Built-In 3 Doors Swing Wardrobe","category_id":1,"SKU":null,"type":"component","description":"with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w\/ LED strip & 2nos 13A plugpoints","product_retail_price":700,"product_cost_of_good_sold":415,"product_excluded_price":610,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":3,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":4,"name":"Built-In Study Table Set","category_id":1,"SKU":null,"type":"component","description":"750mm (W) x 750mm (H) x 480mm (D) Fabricated w\/ 13A plugpoints","product_retail_price":300,"product_cost_of_good_sold":230,"product_excluded_price":250,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":4,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":5,"name":"Built-In Wall-Mounted Cabinet Unit","category_id":1,"SKU":null,"type":"component","description":"Fabricated w\/ LED Strip","product_retail_price":200,"product_cost_of_good_sold":0,"product_excluded_price":130,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":5,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":6,"name":"Goodnite Branded - 10\" Queen-sized mattress with 10 years warranty","category_id":1,"SKU":null,"type":"component","description":"Bathroom Wall Mirror","product_retail_price":800,"product_cost_of_good_sold":500,"product_excluded_price":600,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":6,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":7,"name":"Protector, Pillow, Queen-sized bedsheet set with comforter","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":300,"product_cost_of_good_sold":100,"product_excluded_price":200,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":7,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":8,"name":"Optimal-Designed Writing Chair","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":150,"product_cost_of_good_sold":100,"product_excluded_price":100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":8,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":9,"name":"Semi blackout full length curtain","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":500,"product_cost_of_good_sold":400,"product_excluded_price":400,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":9,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":10,"name":"Soft LED lighting","category_id":1,"SKU":null,"type":"component","description":"(2 downlights & 1 track light)","product_retail_price":180,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":10,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":11,"name":"Supply and install a branded ceiling fan","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":180,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":11,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":12,"name":"Designer-Approved Decorative set","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":150,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":12,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":62,"name":"Manpower cost for M&E AND Painting","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":700,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":62,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":65,"name":"Roundup for Queen-Sized Bedroom","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":920,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":3,"product_id":65,"quantity":1,"visibility":0,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":4,"name":"Single-Sized Bedroom","description":"This is the Single-Sized Bedroom","total_price":5500,"products":[{"id":14,"name":"Accent Wall - Designer-look painting","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":300,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":14,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":15,"name":"Built-In Single-sized Bedhead & Bedframe","category_id":1,"SKU":null,"type":"component","description":"with 2nos Soft-Close System Drawers, Fabricated w\/ LED strip & 13A plugpoint","product_retail_price":400,"product_cost_of_good_sold":280,"product_excluded_price":380,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":15,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":16,"name":"Built-In 2 Doors Swing Wardrobe","category_id":1,"SKU":null,"type":"component","description":"with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w\/ LED strip & 2nos 13A plugpoints","product_retail_price":500,"product_cost_of_good_sold":290,"product_excluded_price":480,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":16,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":17,"name":"Built-In Study Table Set","category_id":1,"SKU":null,"type":"component","description":"750mm (W) x 750mm (H) x 480mm (D) Fabricated w\/ 13A plugpoints","product_retail_price":300,"product_cost_of_good_sold":230,"product_excluded_price":250,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":17,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":18,"name":"Built-In Wall-Mounted Cabinet Unit","category_id":1,"SKU":null,"type":"component","description":"Fabricated w\/ LED Strip","product_retail_price":200,"product_cost_of_good_sold":0,"product_excluded_price":130,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":18,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":19,"name":"Goodnite Branded - 10\" Single-sized mattress with 10 years warranty","category_id":1,"SKU":null,"type":"component","description":"Damask Fabric w\/ Posture Spring System, Non-Flip Tech","product_retail_price":800,"product_cost_of_good_sold":350,"product_excluded_price":638,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":19,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":20,"name":"Protector, Pillow, Single-sized bedsheet set with comforter","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":180,"product_cost_of_good_sold":100,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":20,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":21,"name":"Optimal-Designed Writing Chair","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":150,"product_cost_of_good_sold":100,"product_excluded_price":100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":21,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":22,"name":"Semi blackout full length curtain","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":500,"product_cost_of_good_sold":400,"product_excluded_price":400,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":22,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":23,"name":"Soft LED lighting","category_id":1,"SKU":null,"type":"component","description":"(2 downlights & 1 track light)","product_retail_price":180,"product_cost_of_good_sold":150,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":23,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":24,"name":"Supply and install a branded ceiling fan","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":180,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":24,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":25,"name":"Designer-Approved Decorative set","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":150,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":25,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":62,"name":"Manpower cost for M&E AND Painting","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":700,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":62,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":66,"name":"Roundup for Single-Sized Bedroom","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":890,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":4,"product_id":66,"quantity":1,"visibility":0,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":5,"name":"Kitchen","description":"This is the Kitchen","total_price":6500,"products":[{"id":27,"name":"Soft LED lighting (2 lights \/ track lights) & required wiring works","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":480,"product_cost_of_good_sold":0,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":5,"product_id":27,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":28,"name":"5ft - 7ft Built-In Kitchen Cabinet Package with LED Ambient Strip","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":5000,"product_cost_of_good_sold":2600,"product_excluded_price":3000,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":5,"product_id":28,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":62,"name":"Manpower cost for M&E AND Painting","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":700,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":5,"product_id":62,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":67,"name":"Roundup for Kitchen","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":320,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":5,"product_id":67,"quantity":1,"visibility":0,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":6,"name":"Dining, Yard & Foyer","description":"This is the Dining, Yard & Foyer","total_price":4600,"products":[{"id":29,"name":"Dining bar table","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":500,"product_cost_of_good_sold":480,"product_excluded_price":480,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":29,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":30,"name":"Dining chairs","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":120,"product_cost_of_good_sold":100,"product_excluded_price":100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":30,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":31,"name":"Built-In Shoe Cabinet (W:900mm x H:1200mm x D:350mm)","category_id":1,"SKU":null,"type":"component","description":"with Bench (W:500mm x H:450mm x D:350mm)","product_retail_price":430,"product_cost_of_good_sold":400,"product_excluded_price":400,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":31,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":32,"name":"Supply and install cloth hanger","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":180,"product_cost_of_good_sold":150,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":32,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":33,"name":"Fire extinguishers (Dining)","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":80,"product_cost_of_good_sold":50,"product_excluded_price":50,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":33,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":34,"name":"Soft LED lighting (Dining)","category_id":1,"SKU":null,"type":"component","description":"(Downlights & Pendant Light)","product_retail_price":250,"product_cost_of_good_sold":210,"product_excluded_price":210,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":34,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":35,"name":"Additional wiring-related work for plugs for Wifi & CCTV","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":30,"product_cost_of_good_sold":25,"product_excluded_price":25,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":35,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":36,"name":"Fire extinguishers (Commune Living Space)","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":250,"product_cost_of_good_sold":200,"product_excluded_price":200,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":36,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":62,"name":"Manpower cost for M&E AND Painting","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":700,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":62,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":68,"name":"Roundup for Dining, Yard & Foyer","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":1610,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":6,"product_id":68,"quantity":1,"visibility":0,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":7,"name":"Commune Living Space","description":"This is the Commune Living Space","total_price":8850,"products":[{"id":29,"name":"Dining bar table","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":500,"product_cost_of_good_sold":480,"product_excluded_price":480,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":29,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":30,"name":"Dining chairs","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":120,"product_cost_of_good_sold":100,"product_excluded_price":100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":30,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":31,"name":"Built-In Shoe Cabinet (W:900mm x H:1200mm x D:350mm)","category_id":1,"SKU":null,"type":"component","description":"with Bench (W:500mm x H:450mm x D:350mm)","product_retail_price":430,"product_cost_of_good_sold":400,"product_excluded_price":400,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":31,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":32,"name":"Supply and install cloth hanger","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":180,"product_cost_of_good_sold":150,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":32,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":36,"name":"Fire extinguishers (Commune Living Space)","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":250,"product_cost_of_good_sold":200,"product_excluded_price":200,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":36,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":37,"name":"Soft LED lighting (Commune Living Space)","category_id":1,"SKU":null,"type":"component","description":"(Downlights & Pendant Light)","product_retail_price":220,"product_cost_of_good_sold":200,"product_excluded_price":200,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":37,"quantity":2,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":35,"name":"Additional wiring-related work for plugs for Wifi & CCTV","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":30,"product_cost_of_good_sold":25,"product_excluded_price":25,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":35,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":25,"name":"Designer-Approved Decorative set","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":200,"product_cost_of_good_sold":150,"product_excluded_price":150,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":25,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":38,"name":"Supply and install branded ceiling fan","category_id":1,"SKU":null,"type":"component","description":"(Living Space & Dining Space)","product_retail_price":200,"product_cost_of_good_sold":180,"product_excluded_price":180,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":38,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":41,"name":"Tatami Living Platform","category_id":1,"SKU":null,"type":"component","description":"(W: 910mm x L: 1900mm x H: 300mm)","product_retail_price":800,"product_cost_of_good_sold":780,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":41,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":40,"name":"Curtain (semi blackout full length) with track","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":550,"product_cost_of_good_sold":500,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":40,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":42,"name":"Convertible Tatami Bench","category_id":1,"SKU":null,"type":"component","description":"(W: 2400mm x H: 450mm x D: 400mm)","product_retail_price":750,"product_cost_of_good_sold":730,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":42,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":62,"name":"Manpower cost for M&E AND Painting","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":700,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":62,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":69,"name":"Roundup for Commune Living Space","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":3250,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":7,"product_id":69,"quantity":1,"visibility":0,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":8,"name":"Toilet Furnishing (Freebies)","description":"This is the Toilet Furnishing (Freebies)","total_price":0,"products":[{"id":44,"name":"Supply and install downlight","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":0,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":8,"product_id":44,"quantity":2,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":45,"name":"Supply and install on wall mirror","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":0,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":8,"product_id":45,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":46,"name":"Supply and install water heater","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":0,"product_cost_of_good_sold":180,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":8,"product_id":46,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":47,"name":"Supply and install on clothes hanger","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":0,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":8,"product_id":47,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":9,"name":"Electrical Appliances Bundle set","description":"This is the Electrical Appliances Bundle set","total_price":8575,"products":[{"id":48,"name":"Supply & install 8kg washer front load with IoT Enabled","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":1500,"product_cost_of_good_sold":1100,"product_excluded_price":1100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":48,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":49,"name":"Supply & install 8kg dryer front load with IoT Enabled","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":1500,"product_cost_of_good_sold":1100,"product_excluded_price":1100,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":49,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":50,"name":"Supply & install Combo 2 In 1 Washer Dryer with IoT Enabled","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":0,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":50,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":51,"name":"Supply and Install hood and hob","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":2000,"product_cost_of_good_sold":1500,"product_excluded_price":1500,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":51,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":52,"name":"Supply & Install iBilikPlus IoT Enabled Smart Main Door Lock","category_id":1,"SKU":null,"type":"service","description":"with double latches","product_retail_price":545,"product_cost_of_good_sold":600,"product_excluded_price":600,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":52,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":53,"name":"Supply and install CCTV in dining area","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":250,"product_cost_of_good_sold":180,"product_excluded_price":180,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":53,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":54,"name":"Microwave","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":400,"product_cost_of_good_sold":230,"product_excluded_price":230,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":54,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":55,"name":"Hot & Warm Water Dispenser c\/w 4 Layer Korea Technology Filtration","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":380,"product_cost_of_good_sold":266,"product_excluded_price":266,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":55,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":56,"name":"2 door mini bar Fridge","category_id":1,"SKU":null,"type":"component","description":null,"product_retail_price":500,"product_cost_of_good_sold":400,"product_excluded_price":400,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":9,"product_id":56,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":10,"name":"Air Conditioning & Piping Works","description":"This is the Air Conditioning & Piping Works","total_price":5450,"products":[{"id":57,"name":"Supply and install 1 hp aircond without copper piping - midea\/ gree\/ hisense","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":1300,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":10,"product_id":57,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":58,"name":"Relocation of aircond to the partitioned room","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":250,"product_cost_of_good_sold":0,"product_excluded_price":0,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":10,"product_id":58,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]},{"id":11,"name":"IoT for Bedroom Bundle set","description":"This is the IoT for Bedroom Bundle set","total_price":3568,"products":[{"id":59,"name":"Supply & Install iBilikPlus IoT Enabled Smart Room Door Lock","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":350,"product_cost_of_good_sold":297,"product_excluded_price":297,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":11,"product_id":59,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":60,"name":"Supply & Install iBilikPlus IoT Enabled Smart Meter connected to WHOLE room","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":500,"product_cost_of_good_sold":199,"product_excluded_price":199,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":11,"product_id":60,"quantity":4,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}},{"id":61,"name":"Supply & Install Smart WIFI G2 Gateway Hub","category_id":1,"SKU":null,"type":"service","description":null,"product_retail_price":168,"product_cost_of_good_sold":168,"product_excluded_price":168,"status":"available","created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z","pivot":{"package_id":11,"product_id":61,"quantity":1,"visibility":1,"included":1,"isOriginal":1,"created_at":"2024-10-14T01:29:26.000000Z","updated_at":"2024-10-14T01:29:26.000000Z"}}]}]'
        ]);
    }
}
