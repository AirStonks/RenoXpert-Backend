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
            'name' => 'dev',
            'email' => 'dev@gmail.com',
            'password' => 'developer',
            'phone_no' => '0123456789',
            'type' => 'admin',
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


        // $package1 = Package::create([
        //     'name' => 'Partition Queen-Sized Bedroom',
        //     'description' => 'This is the Partition Queen-Sized Bedroom',
        //     'total_price' => 3150.00
        // ]);

        // $package1->products()->attach(1, ['quantity' => 1]);
        // $package1->products()->attach(2, ['quantity' => 1]);
        // $package1->products()->attach(3, ['quantity' => 1]);
        // $package1->products()->attach(4, ['quantity' => 1]);
        // $package1->products()->attach(5, ['quantity' => 1]);
        // $package1->products()->attach(6, ['quantity' => 1]);
        // $package1->products()->attach(7, ['quantity' => 1]);

        // $package2 = Package::create([
        //     'name' => 'Partition Single-Sized Bedroom',
        //     'description' => 'This is the Partition Single-Sized Bedroom',
        //     'total_price' => 2600.00
        // ]);

        // $package2->products()->attach(8, ['quantity' => 1]);
        // $package2->products()->attach(9, ['quantity' => 1]);
        // $package2->products()->attach(10, ['quantity' => 1]);
        // $package2->products()->attach(11, ['quantity' => 1]);
        // $package2->products()->attach(12, ['quantity' => 1]);
        // $package2->products()->attach(13, ['quantity' => 1]);

        Contact::create([
            'name' => 'CK Chang',
            'email' => 'ckchang@gmail.com',
            'phone_no' => '01136647745',
            'race' => 'Chinese',
            'gender' => 'Male',
            'nationality' => 'Malaysian',
            'description' => 'some desc',
        ]);

        Property::create([
            'name' => 'The Nest Serviced Apartment',
            'address' => 'RESIDENSI 357 SETAPAK',
            'street' => 'LORONG 2/23D',
            'postcode' => '53300',
            'city' => 'SETAPAK',
            'state' => 'KUALA LUMPUR',
            'description' => 'some desc',
        ]);

        // Quotation::create([
        //     'name' => 'MV-TA01',
        //     'description' => 'M Vertica Type A',
        //     'total_amount' => 880.00,
        //     'metadata' => '[{"id":1,"name":"Bedroom Package (M)","description":"This is the bedroom package for medium bedroom (M)","total_price":420,"products":[{"id":1,"name":"Curtain (MB)","category_id":3,"SKU":"981234745678","type":"component","description":"Curtain for medium bedroom","price":100,"premium_price":null,"status":"available","created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z","pivot":{"package_id":1,"product_id":1,"quantity":1,"created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z"}},{"id":2,"name":"Bedroom Wiring","category_id":3,"SKU":"186723545825","type":"service","description":"Electric, cable wiring service","price":150,"premium_price":null,"status":"available","created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z","pivot":{"package_id":1,"product_id":2,"quantity":1,"created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z"}},{"id":4,"name":"Optimal-Designed Writing Chair","category_id":3,"SKU":"916323481825","type":"component","description":"Chair for bedroom","price":85,"premium_price":null,"status":"available","created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z","pivot":{"package_id":1,"product_id":4,"quantity":2,"created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z"}}]},{"id":2,"name":"Random Package","description":"This is a random package","total_price":460,"products":[{"id":3,"name":"Door stopper","category_id":2,"SKU":"656323545825","type":"component","description":"Bedroom Door stopper","price":80,"premium_price":null,"status":"available","created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z","pivot":{"package_id":2,"product_id":3,"quantity":3,"created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z"}},{"id":5,"name":"Door bell","category_id":1,"SKU":"496773845584","type":"component","description":"Foyer door bell","price":50,"premium_price":null,"status":"available","created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z","pivot":{"package_id":2,"product_id":5,"quantity":2,"created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z"}},{"id":6,"name":"Wall mirror","category_id":4,"SKU":"456738642684","type":"component","description":"Bathroom Wall Mirror","price":120,"premium_price":null,"status":"available","created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z","pivot":{"package_id":2,"product_id":6,"quantity":1,"created_at":"2024-10-01T03:25:55.000000Z","updated_at":"2024-10-01T03:25:55.000000Z"}}]}]'
        // ]);
    }
}
