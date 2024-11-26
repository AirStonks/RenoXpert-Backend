<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Property;
use App\Models\Quotation;
use App\Models\PMCategory;
use App\Models\ProductSupply;
use App\Models\OrderQuotation;
use App\Models\ProductInstall;
use Illuminate\Database\Seeder;
use App\Models\RegistrationForm;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Developer (CK)',
            'name_first' => 'Developer',
            'name_last' => '(CK)',
            'email' => 'developer@belive.asia',
            'password' => 'M.O0O]Q(a:HBR(Y.)O',
            'phone_no' => '1136647745',
            'type' => 'super-admin',
        ]);

        User::factory()->create([
            'name' => 'Lee Seng Hee',
            'name_first' => 'Lee',
            'name_last' => 'Seng Hee',
            'email' => 'shlee@belive.asia',
            'password' => 'aqaV5KruKZ6dYyO+mH',
            'phone_no' => '1118882881',
            'type' => 'super-admin',
        ]);

        User::factory()->create([
            'name' => 'Test Technician',
            'name_first' => 'Test',
            'name_last' => 'Technician',
            'email' => 'testtechnician@gmail.com',
            'password' => '12345678',
            'phone_no' => '1122334455',
            'type' => 'technician',
        ]);

        Address::create([
            'address_1' => 'abcd1234',
            'address_2' => 'abcd1234',
            'city' => 'PJ',
            'state' => 'Selangor',
            'postcode' => '54300',
        ]);

        User::create([
            'name' => 'Test Owner',
            'name_first' => 'Test',
            'name_last' => 'Owner',
            'salutations' => 'mr',
            'ic' => '001111-22-3333',
            'email' => 'testuser@gmail.com',
            'password' => '12345678',
            'phone_no' => '1111476550',
            'type' => 'owner',
            'address_id' => 1,
        ]);

        RegistrationForm::create([
            'form_no' => '12091795',
            'salutations' => 'mr',
            'name_first' => 'Test',
            'name_last' => 'User',
            'name_preferred' => 'TestUser',
            'email' => 'testuser@gmail.com',
            'country_code' => '+60',
            'phone_no' => '1111476550',
            'address_1' => 'abcd1234',
            'address_2' => 'abcd1234',
            'city' => 'PJ',
            'state' => 'Selangor',
            'postcode' => '54300',
            'ic' => '001111-22-3333',
            'property_name' => '2',
            'other_property_name' => null,
            'block' => 'B',
            'level' => '15',
            'unit' => '15',
            'layout_type' => 'A',
            'sqft' => '1000',
            'metadata' => '{"furnishing":{"foyer_entrance":{"grille_door":"furnished","digital_lock":"furnished","shoe_cabinet":"furnished","lights":"furnished","other":""},"kitchen":{"kitchen_cabinet":"furnished","kitchen_island":"furnished","sink_tap":"furnished","hood_hob":"furnished","microwave":"not-furnish","oven":"not-furnish","water_dispenser":"furnished","fridge":"furnished","lights":"furnished","other":""},"yard":{"washer":"furnished","dryer":"furnished","lights":"furnished","other":""},"dining":{"dining_table_chairs":"furnished","lights":"furnished","fan":"furnished","other":""},"living":{"sofa":"furnished","coffee_table":"furnished","tv":"furnished","tv_cabinet":"furnished","fan":"furnished","lights":"furnished","ac":"furnished","other":""}},"questions":{"quest_1":"3","quest_2":"2","quest_3":"done","quest_4":"done","quest_5":"done","quest_6":"done","quest_7":"done","quest_8":"yes"}}',
            'attachments' => null,
            'status' => 'approved'
        ]);

        PMCategory::create([
            'id' => 1,
            'name' => 'Others',
            'description' => 'Other category products goes this category',
        ]);

        PMCategory::create([
            'id' => 2,
            'name' => 'Furniture',
            'description' => '',
        ]);

        PMCategory::create([
            'id' => 3,
            'name' => 'Wiring',
            'description' => '',
        ]);

        PMCategory::create([
            'id' => 4,
            'name' => 'Painting',
            'description' => '',
        ]);

        PMCategory::create([
            'id' => 5,
            'name' => 'Curtain',
            'description' => '',
        ]);

        Product::create([
            'id' => 1,
            'name' => 'Accent Wall - Designer-look painting',
            'pm_category_id' => 4,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 1,
            'retail_price' => 110.0,
            'cogs' => 50.0,
            'excluded_price' => 50.0,
        ]);

        ProductInstall::create([
            'product_id' => 1,
            'retail_price' => 190.0,
            'cogs' => 80.0,
            'excluded_price' => 100.0,
        ]);

        Product::create([
            'id' => 2,
            'name' => 'Built-In Queen-sized Bedhead & Bedframe',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => 'with 2nos Soft-Close System Drawers, Fabricated w/ LED strip & 13A plugpoint',
            'uom' => 'set'
        ]);

        ProductSupply::create([
            'product_id' => 2,
            'retail_price' => 320.0,
            'cogs' => 250.0,
            'excluded_price' => 280.0,
        ]);

        ProductInstall::create([
            'product_id' => 2,
            'retail_price' => 230.0,
            'cogs' => 140.0,
            'excluded_price' => 180.0,
        ]);

        Product::create([
            'id' => 3,
            'name' => 'Built-In 3 Doors Swing Wardrobe',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => 'with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w/ LED strip & 2nos 13A plugpoints',
            'uom' => 'set'
        ]);

        ProductSupply::create([
            'product_id' => 3,
            'retail_price' => 480.0,
            'cogs' => 255.0,
            'excluded_price' => 410.0,
        ]);

        ProductInstall::create([
            'product_id' => 3,
            'retail_price' => 220.0,
            'cogs' => 160.0,
            'excluded_price' => 200.0,
        ]);

        Product::create([
            'id' => 4,
            'name' => 'Built-In Study Table Set',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '750mm (W) x 750mm (H) x 480mm (D) Fabricated w/ 13A plugpoints',
            'uom' => 'set'
        ]);

        ProductSupply::create([
            'product_id' => 4,
            'retail_price' => 230.0,
            'cogs' => 185.0,
            'excluded_price' => 200.0,
        ]);

        ProductInstall::create([
            'product_id' => 4,
            'retail_price' => 70.0,
            'cogs' => 45.0,
            'excluded_price' => 50.0,
        ]);

        Product::create([
            'id' => 5,
            'name' => 'Built-In Wall-Mounted Cabinet Unit',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => 'Fabricated w/ LED Strip',
            'uom' => 'unit'
        ]);

        ProductSupply::create([
            'product_id' => 5,
            'retail_price' => 130.0,
            'cogs' => 0.0,
            'excluded_price' => 85.0,
        ]);

        ProductInstall::create([
            'product_id' => 5,
            'retail_price' => 70.0,
            'cogs' => 0.0,
            'excluded_price' => 45.0,
        ]);

        // Product 6
        Product::create([
            'id' => 6,
            'name' => 'Goodnite Branded - 10" Queen-sized mattress with 10 years warranty',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => 'Bathroom Wall Mirror',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 6,
            'retail_price' => 800.00 * (3 / 5), // 480.00
            'cogs' => 500.00 * (3 / 5), // 300.00
            'excluded_price' => 600.00 * (3 / 5), // 360.00
        ]);

        ProductInstall::create([
            'product_id' => 6,
            'retail_price' => 800.00 * (2 / 5), // 320.00
            'cogs' => 500.00 * (2 / 5), // 200.00
            'excluded_price' => 600.00 * (2 / 5), // 240.00
        ]);

        // Product 7
        Product::create([
            'id' => 7,
            'name' => 'Protector, Pillow, Queen-sized bedsheet set with comforter',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 7,
            'retail_price' => 300.00 * (3 / 5), // 180.00
            'cogs' => 100.00 * (3 / 5), // 60.00
            'excluded_price' => 200.00 * (3 / 5), // 120.00
        ]);

        ProductInstall::create([
            'product_id' => 7,
            'retail_price' => 300.00 * (2 / 5), // 120.00
            'cogs' => 100.00 * (2 / 5), // 40.00
            'excluded_price' => 200.00 * (2 / 5), // 80.00
        ]);

        // Product 8
        Product::create([
            'id' => 8,
            'name' => 'Optimal-Designed Writing Chair',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 8,
            'retail_price' => 150.00 * (3 / 5), // 90.00
            'cogs' => 100.00 * (3 / 5), // 60.00
            'excluded_price' => 100.00 * (3 / 5), // 60.00
        ]);

        ProductInstall::create([
            'product_id' => 8,
            'retail_price' => 150.00 * (2 / 5), // 60.00
            'cogs' => 100.00 * (2 / 5), // 40.00
            'excluded_price' => 100.00 * (2 / 5), // 40.00
        ]);

        // Product 9
        Product::create([
            'id' => 9,
            'name' => 'Semi blackout full length curtain',
            'pm_category_id' => 5,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 9,
            'retail_price' => 500.00 * (3 / 5), // 300.00
            'cogs' => 400.00 * (3 / 5), // 240.00
            'excluded_price' => 400.00 * (3 / 5), // 240.00
        ]);

        ProductInstall::create([
            'product_id' => 9,
            'retail_price' => 500.00 * (2 / 5), // 200.00
            'cogs' => 400.00 * (2 / 5), // 160.00
            'excluded_price' => 400.00 * (2 / 5), // 160.00
        ]);

        // Product 10
        Product::create([
            'id' => 10,
            'name' => 'Soft LED lighting',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '(2 downlights & 1 track light)',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 10,
            'retail_price' => 180.00 * (3 / 5), // 108.00
            'cogs' => 150.00 * (3 / 5), // 90.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 10,
            'retail_price' => 180.00 * (2 / 5), // 72.00
            'cogs' => 150.00 * (2 / 5), // 60.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);


        // Product 11
        Product::create([
            'id' => 11,
            'name' => 'Supply and install a branded ceiling fan',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 11,
            'retail_price' => 200.00 * (3 / 5), // 120.00
            'cogs' => 180.00 * (3 / 5), // 108.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 11,
            'retail_price' => 200.00 * (2 / 5), // 80.00
            'cogs' => 180.00 * (2 / 5), // 72.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 12
        Product::create([
            'id' => 12,
            'name' => 'Designer-Approved Decorative set',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 12,
            'retail_price' => 200.00 * (3 / 5), // 120.00
            'cogs' => 150.00 * (3 / 5), // 90.00
            'excluded_price' => 150.00 * (3 / 5), // 90.00
        ]);

        ProductInstall::create([
            'product_id' => 12,
            'retail_price' => 200.00 * (2 / 5), // 80.00
            'cogs' => 150.00 * (2 / 5), // 60.00
            'excluded_price' => 150.00 * (2 / 5), // 60.00
        ]);

        // Product 13
        Product::create([
            'id' => 13,
            'name' => '9.5mm drywall partition, with skim, paint, knobs, hinges, and wooden door',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => ' (< 150sqft)',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 13,
            'retail_price' => 2000.00 * (3 / 5), // 1200.00
            'cogs' => 1600.00 * (3 / 5), // 960.00
            'excluded_price' => 1800.00 * (3 / 5), // 1080.00
        ]);

        ProductInstall::create([
            'product_id' => 13,
            'retail_price' => 2000.00 * (2 / 5), // 800.00
            'cogs' => 1600.00 * (2 / 5), // 640.00
            'excluded_price' => 1800.00 * (2 / 5), // 720.00
        ]);


        // // ---------------------------------------------------

        // Product 14
        Product::create([
            'id' => 14,
            'name' => 'Accent Wall - Designer-look painting',
            'pm_category_id' => 4,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 14,
            'retail_price' => 300.00 * (3 / 5), // 180.00
            'cogs' => 150.00 * (3 / 5), // 90.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 14,
            'retail_price' => 300.00 * (2 / 5), // 120.00
            'cogs' => 150.00 * (2 / 5), // 60.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 15
        Product::create([
            'id' => 15,
            'name' => 'Built-In Single-sized Bedhead & Bedframe',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => 'with 2nos Soft-Close System Drawers, Fabricated w/ LED strip & 13A plugpoint',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 15,
            'retail_price' => 400.00 * (3 / 5), // 240.00
            'cogs' => 280.00 * (3 / 5), // 168.00
            'excluded_price' => 380.00 * (3 / 5), // 228.00
        ]);

        ProductInstall::create([
            'product_id' => 15,
            'retail_price' => 400.00 * (2 / 5), // 160.00
            'cogs' => 280.00 * (2 / 5), // 112.00
            'excluded_price' => 380.00 * (2 / 5), // 152.00
        ]);

        // Product 16
        Product::create([
            'id' => 16,
            'name' => 'Built-In 2 Doors Swing Wardrobe',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => 'with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D), Fabricated w/ LED strip & 2nos 13A plugpoints',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 16,
            'retail_price' => 500.00 * (3 / 5), // 300.00
            'cogs' => 290.00 * (3 / 5), // 174.00
            'excluded_price' => 480.00 * (3 / 5), // 288.00
        ]);

        ProductInstall::create([
            'product_id' => 16,
            'retail_price' => 500.00 * (2 / 5), // 200.00
            'cogs' => 290.00 * (2 / 5), // 116.00
            'excluded_price' => 480.00 * (2 / 5), // 192.00
        ]);

        // Product 17
        Product::create([
            'id' => 17,
            'name' => 'Built-In Study Table Set',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => '750mm (W) x 750mm (H) x 480mm (D) Fabricated w/ 13A plugpoints',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 17,
            'retail_price' => 300.00 * (3 / 5), // 180.00
            'cogs' => 230.00 * (3 / 5), // 138.00
            'excluded_price' => 250.00 * (3 / 5), // 150.00
        ]);

        ProductInstall::create([
            'product_id' => 17,
            'retail_price' => 300.00 * (2 / 5), // 120.00
            'cogs' => 230.00 * (2 / 5), // 92.00
            'excluded_price' => 250.00 * (2 / 5), // 100.00
        ]);

        // Product 18
        Product::create([
            'id' => 18,
            'name' => 'Built-In Wall-Mounted Cabinet Unit',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => 'Fabricated w/ LED Strip',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 18,
            'retail_price' => 200.00 * (3 / 5), // 120.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 130.00 * (3 / 5), // 78.00
        ]);

        ProductInstall::create([
            'product_id' => 18,
            'retail_price' => 200.00 * (2 / 5), // 80.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 130.00 * (2 / 5), // 52.00
        ]);

        // Product 19
        Product::create([
            'id' => 19,
            'name' => 'Goodnite Branded - 10" Single-sized mattress with 10 years warranty',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => 'Damask Fabric w/ Posture Spring System, Non-Flip Tech',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 19,
            'retail_price' => 800.00 * (3 / 5), // 480.00
            'cogs' => 350.00 * (3 / 5), // 210.00
            'excluded_price' => 638.00 * (3 / 5), // 382.80
        ]);

        ProductInstall::create([
            'product_id' => 19,
            'retail_price' => 800.00 * (2 / 5), // 320.00
            'cogs' => 350.00 * (2 / 5), // 140.00
            'excluded_price' => 638.00 * (2 / 5), // 255.20
        ]);

        // Product 20
        Product::create([
            'id' => 20,
            'name' => 'Protector, Pillow, Single-sized bedsheet set with comforter',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 20,
            'retail_price' => 180.00 * (3 / 5), // 108.00
            'cogs' => 100.00 * (3 / 5), // 60.00
            'excluded_price' => 150.00 * (3 / 5), // 90.00
        ]);

        ProductInstall::create([
            'product_id' => 20,
            'retail_price' => 180.00 * (2 / 5), // 72.00
            'cogs' => 100.00 * (2 / 5), // 40.00
            'excluded_price' => 150.00 * (2 / 5), // 60.00
        ]);

        // Product 21
        Product::create([
            'id' => 21,
            'name' => 'Optimal-Designed Writing Chair',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 21,
            'retail_price' => 150.00 * (3 / 5), // 90.00
            'cogs' => 100.00 * (3 / 5), // 60.00
            'excluded_price' => 100.00 * (3 / 5), // 60.00
        ]);

        ProductInstall::create([
            'product_id' => 21,
            'retail_price' => 150.00 * (2 / 5), // 60.00
            'cogs' => 100.00 * (2 / 5), // 40.00
            'excluded_price' => 100.00 * (2 / 5), // 40.00
        ]);

        // Product 22
        Product::create([
            'id' => 22,
            'name' => 'Semi blackout full length curtain',
            'pm_category_id' => 5,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 22,
            'retail_price' => 500.00 * (3 / 5), // 300.00
            'cogs' => 400.00 * (3 / 5), // 240.00
            'excluded_price' => 400.00 * (3 / 5), // 240.00
        ]);

        ProductInstall::create([
            'product_id' => 22,
            'retail_price' => 500.00 * (2 / 5), // 200.00
            'cogs' => 400.00 * (2 / 5), // 160.00
            'excluded_price' => 400.00 * (2 / 5), // 160.00
        ]);


        // Product 23
        Product::create([
            'id' => 23,
            'name' => 'Soft LED lighting',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '(2 downlights & 1 track light)',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 23,
            'retail_price' => 180.00 * (3 / 5), // 108.00
            'cogs' => 150.00 * (3 / 5), // 90.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 23,
            'retail_price' => 180.00 * (2 / 5), // 72.00
            'cogs' => 150.00 * (2 / 5), // 60.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);;

        // Product 24
        Product::create([
            'id' => 24,
            'name' => 'Supply and install a branded ceiling fan',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 24,
            'retail_price' => 200.00 * (3 / 5), // 120.00
            'cogs' => 180.00 * (3 / 5), // 108.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 24,
            'retail_price' => 200.00 * (2 / 5), // 80.00
            'cogs' => 180.00 * (2 / 5), // 72.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 25
        Product::create([
            'id' => 25,
            'name' => 'Designer-Approved Decorative set',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 25,
            'retail_price' => 200.00 * (3 / 5), // 120.00
            'cogs' => 150.00 * (3 / 5), // 90.00
            'excluded_price' => 150.00 * (3 / 5), // 90.00
        ]);

        ProductInstall::create([
            'product_id' => 25,
            'retail_price' => 200.00 * (2 / 5), // 80.00
            'cogs' => 150.00 * (2 / 5), // 60.00
            'excluded_price' => 150.00 * (2 / 5), // 60.00
        ]);

        // Product 26
        Product::create([
            'id' => 26,
            'name' => '9.5mm drywall partition, with skim, paint, knobs, hinges, and wooden door',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '(< 150sqft)',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 26,
            'retail_price' => 1800.00 * (3 / 5), // 1080.00
            'cogs' => 1600.00 * (3 / 5), // 960.00
            'excluded_price' => 1600.00 * (3 / 5), // 960.00
        ]);

        ProductInstall::create([
            'product_id' => 26,
            'retail_price' => 1800.00 * (2 / 5), // 720.00
            'cogs' => 1600.00 * (2 / 5), // 640.00
            'excluded_price' => 1600.00 * (2 / 5), // 640.00
        ]);


        // // ===================================================================
        // // ===================================================================
        // // ===================================================================

        // Product 27
        Product::create([
            'id' => 27,
            'name' => 'Soft LED lighting (2 lights / track lights) & required wiring works',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 27,
            'retail_price' => 480.00 * (3 / 5), // 288.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 150.00 * (3 / 5), // 90.00
        ]);

        ProductInstall::create([
            'product_id' => 27,
            'retail_price' => 480.00 * (2 / 5), // 192.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 150.00 * (2 / 5), // 60.00
        ]);

        // Product 28
        Product::create([
            'id' => 28,
            'name' => '5ft - 7ft Built-In Kitchen Cabinet Package with LED Ambient Strip',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'package',
        ]);

        ProductSupply::create([
            'product_id' => 28,
            'retail_price' => 5000.00 * (3 / 5), // 3000.00
            'cogs' => 2600.00 * (3 / 5), // 1560.00
            'excluded_price' => 3000.00 * (3 / 5), // 1800.00
        ]);

        ProductInstall::create([
            'product_id' => 28,
            'retail_price' => 5000.00 * (2 / 5), // 2000.00
            'cogs' => 2600.00 * (2 / 5), // 1040.00
            'excluded_price' => 3000.00 * (2 / 5), // 1200.00
        ]);


        // // ===================================================================
        // // ===================================================================
        // // ===================================================================

        // Product 29
        Product::create([
            'id' => 29,
            'name' => 'Dining bar table',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 29,
            'retail_price' => 500.00 * (3 / 5), // 300.00
            'cogs' => 480.00 * (3 / 5), // 288.00
            'excluded_price' => 480.00 * (3 / 5), // 288.00
        ]);

        ProductInstall::create([
            'product_id' => 29,
            'retail_price' => 500.00 * (2 / 5), // 200.00
            'cogs' => 480.00 * (2 / 5), // 192.00
            'excluded_price' => 480.00 * (2 / 5), // 192.00
        ]);

        // Product 30
        Product::create([
            'id' => 30,
            'name' => 'Dining chairs',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 30,
            'retail_price' => 120.00 * (3 / 5), // 72.00
            'cogs' => 100.00 * (3 / 5), // 60.00
            'excluded_price' => 100.00 * (3 / 5), // 60.00
        ]);

        ProductInstall::create([
            'product_id' => 30,
            'retail_price' => 120.00 * (2 / 5), // 48.00
            'cogs' => 100.00 * (2 / 5), // 40.00
            'excluded_price' => 100.00 * (2 / 5), // 40.00
        ]);

        // Product 31
        Product::create([
            'id' => 31,
            'name' => 'Built-In Shoe Cabinet (W:900mm x H:1200mm x D:350mm)',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => 'with Bench (W:500mm x H:450mm x D:350mm)',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 31,
            'retail_price' => 430.00 * (3 / 5), // 258.00
            'cogs' => 400.00 * (3 / 5), // 240.00
            'excluded_price' => 400.00 * (3 / 5), // 240.00
        ]);

        ProductInstall::create([
            'product_id' => 31,
            'retail_price' => 430.00 * (2 / 5), // 172.00
            'cogs' => 400.00 * (2 / 5), // 160.00
            'excluded_price' => 400.00 * (2 / 5), // 160.00
        ]);

        // Product 32
        Product::create([
            'id' => 32,
            'name' => 'Supply and install cloth hanger',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 32,
            'retail_price' => 180.00 * (3 / 5), // 108.00
            'cogs' => 150.00 * (3 / 5), // 90.00
            'excluded_price' => 150.00 * (3 / 5), // 90.00
        ]);

        ProductInstall::create([
            'product_id' => 32,
            'retail_price' => 180.00 * (2 / 5), // 72.00
            'cogs' => 150.00 * (2 / 5), // 60.00
            'excluded_price' => 150.00 * (2 / 5), // 60.00
        ]);

        // Product 33
        Product::create([
            'id' => 33,
            'name' => 'Fire extinguishers (Dining)',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 33,
            'retail_price' => 80.00 * (3 / 5), // 48.00
            'cogs' => 50.00 * (3 / 5), // 30.00
            'excluded_price' => 50.00 * (3 / 5), // 30.00
        ]);

        ProductInstall::create([
            'product_id' => 33,
            'retail_price' => 80.00 * (2 / 5), // 32.00
            'cogs' => 50.00 * (2 / 5), // 20.00
            'excluded_price' => 50.00 * (2 / 5), // 20.00
        ]);

        // Product 34
        Product::create([
            'id' => 34,
            'name' => 'Soft LED lighting (Dining)',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '(Downlights & Pendant Light)',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 34,
            'retail_price' => 250.00 * (3 / 5), // 150.00
            'cogs' => 210.00 * (3 / 5), // 126.00
            'excluded_price' => 210.00 * (3 / 5), // 126.00
        ]);

        ProductInstall::create([
            'product_id' => 34,
            'retail_price' => 250.00 * (2 / 5), // 100.00
            'cogs' => 210.00 * (2 / 5), // 84.00
            'excluded_price' => 210.00 * (2 / 5), // 84.00
        ]);

        // Product 35
        Product::create([
            'id' => 35,
            'name' => 'Additional wiring-related work for plugs for Wifi & CCTV',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 35,
            'retail_price' => 30.00 * (3 / 5), // 18.00
            'cogs' => 25.00 * (3 / 5), // 15.00
            'excluded_price' => 25.00 * (3 / 5), // 15.00
        ]);

        ProductInstall::create([
            'product_id' => 35,
            'retail_price' => 30.00 * (2 / 5), // 12.00
            'cogs' => 25.00 * (2 / 5), // 10.00
            'excluded_price' => 25.00 * (2 / 5), // 10.00
        ]);


        // // ===================================================================
        // // ===================================================================
        // // ===================================================================

        // Product 36
        Product::create([
            'id' => 36,
            'name' => 'Fire extinguishers (Commune Living Space)',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 36,
            'retail_price' => 250.00 * (3 / 5), // 150.00
            'cogs' => 200.00 * (3 / 5), // 120.00
            'excluded_price' => 200.00 * (3 / 5), // 120.00
        ]);

        ProductInstall::create([
            'product_id' => 36,
            'retail_price' => 250.00 * (2 / 5), // 100.00
            'cogs' => 200.00 * (2 / 5), // 80.00
            'excluded_price' => 200.00 * (2 / 5), // 80.00
        ]);

        // Product 37
        Product::create([
            'id' => 37,
            'name' => 'Soft LED lighting (Commune Living Space)',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '(Downlights & Pendant Light)',
            'uom' => 'set',
        ]);

        ProductSupply::create([
            'product_id' => 37,
            'retail_price' => 220.00 * (3 / 5), // 132.00
            'cogs' => 200.00 * (3 / 5), // 120.00
            'excluded_price' => 200.00 * (3 / 5), // 120.00
        ]);

        ProductInstall::create([
            'product_id' => 37,
            'retail_price' => 220.00 * (2 / 5), // 88.00
            'cogs' => 200.00 * (2 / 5), // 80.00
            'excluded_price' => 200.00 * (2 / 5), // 80.00
        ]);

        // Product 38
        Product::create([
            'id' => 38,
            'name' => 'Supply and install branded ceiling fan',
            'pm_category_id' => 3,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => '(Living Space & Dining Space)',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 38,
            'retail_price' => 200.00 * (3 / 5), // 120.00
            'cogs' => 180.00 * (3 / 5), // 108.00
            'excluded_price' => 180.00 * (3 / 5), // 108.00
        ]);

        ProductInstall::create([
            'product_id' => 38,
            'retail_price' => 200.00 * (2 / 5), // 80.00
            'cogs' => 180.00 * (2 / 5), // 72.00
            'excluded_price' => 180.00 * (2 / 5), // 72.00
        ]);

        // Product 40
        Product::create([
            'id' => 40,
            'name' => 'Curtain (semi blackout full length) with track',
            'pm_category_id' => 5,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 40,
            'retail_price' => 550.00 * (3 / 5), // 330.00
            'cogs' => 500.00 * (3 / 5), // 300.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 40,
            'retail_price' => 550.00 * (2 / 5), // 220.00
            'cogs' => 500.00 * (2 / 5), // 200.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 41
        Product::create([
            'id' => 41,
            'name' => 'Tatami Living Platform',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => '(W: 910mm x L: 1900mm x H: 300mm)',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 41,
            'retail_price' => 800.00 * (3 / 5), // 480.00
            'cogs' => 780.00 * (3 / 5), // 468.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 41,
            'retail_price' => 800.00 * (2 / 5), // 320.00
            'cogs' => 780.00 * (2 / 5), // 312.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 42
        Product::create([
            'id' => 42,
            'name' => 'Convertible Tatami Bench',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '(W: 2400mm x H: 450mm x D: 400mm)',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 42,
            'retail_price' => 750.00 * (3 / 5), // 450.00
            'cogs' => 730.00 * (3 / 5), // 438.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 42,
            'retail_price' => 750.00 * (2 / 5), // 300.00
            'cogs' => 730.00 * (2 / 5), // 292.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 43
        Product::create([
            'id' => 43,
            'name' => 'Coffee Table',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '(W: 2400mm x H: 450mm x D: 400mm)',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 43,
            'retail_price' => 750.00 * (3 / 5), // 450.00
            'cogs' => 250.00 * (3 / 5), // 150.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 43,
            'retail_price' => 750.00 * (2 / 5), // 300.00
            'cogs' => 250.00 * (2 / 5), // 100.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);


        // // ===================================================================
        // // ===================================================================
        // // ===================================================================

        // Product 44
        Product::create([
            'id' => 44,
            'name' => 'Supply and install downlight',
            'pm_category_id' => 3,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
        ]);

        ProductSupply::create([
            'product_id' => 44,
            'retail_price' => 0.00,
            'cogs' => 0.00,
            'excluded_price' => 0.00,
        ]);

        ProductInstall::create([
            'product_id' => 44,
            'retail_price' => 0.00,
            'cogs' => 0.00,
            'excluded_price' => 0.00,
        ]);

        // Product 45
        Product::create([
            'id' => 45,
            'name' => 'Supply and install on wall mirror',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
        ]);

        ProductSupply::create([
            'product_id' => 45,
            'retail_price' => 0.00,
            'cogs' => 0.00,
            'excluded_price' => 0.00,
        ]);

        ProductInstall::create([
            'product_id' => 45,
            'retail_price' => 0.00,
            'cogs' => 0.00,
            'excluded_price' => 0.00,
        ]);

        // Product 46
        Product::create([
            'id' => 46,
            'name' => 'Supply and install water heater',
            'pm_category_id' => 3,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => '',
        ]);

        ProductSupply::create([
            'product_id' => 46,
            'retail_price' => 0.00,
            'cogs' => 180.00,
            'excluded_price' => 0.00,
        ]);

        ProductInstall::create([
            'product_id' => 46,
            'retail_price' => 0.00,
            'cogs' => 180.00,
            'excluded_price' => 0.00,
        ]);

        // Product 47
        Product::create([
            'id' => 47,
            'name' => 'Supply and install on clothes hanger',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
        ]);

        ProductSupply::create([
            'product_id' => 47,
            'retail_price' => 0.00,
            'cogs' => 0.00,
            'excluded_price' => 0.00,
        ]);

        ProductInstall::create([
            'product_id' => 47,
            'retail_price' => 0.00,
            'cogs' => 0.00,
            'excluded_price' => 0.00,
        ]);


        // // ===================================================================
        // // ===================================================================
        // // ===================================================================

        // Product 48
        Product::create([
            'id' => 48,
            'name' => 'Supply & install 8kg washer front load with IoT Enabled',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 48,
            'retail_price' => 1500.00 * (3 / 5), // 900.00
            'cogs' => 1100.00 * (3 / 5), // 660.00
            'excluded_price' => 1100.00 * (3 / 5), // 660.00
        ]);

        ProductInstall::create([
            'product_id' => 48,
            'retail_price' => 1500.00 * (2 / 5), // 600.00
            'cogs' => 1100.00 * (2 / 5), // 440.00
            'excluded_price' => 1100.00 * (2 / 5), // 440.00
        ]);


        // Product 49
        Product::create([
            'id' => 49,
            'name' => 'Supply & install 8kg dryer front load with IoT Enabled',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 49,
            'retail_price' => 1500.00 * (3 / 5), // 900.00
            'cogs' => 1100.00 * (3 / 5), // 660.00
            'excluded_price' => 1100.00 * (3 / 5), // 660.00
        ]);

        ProductInstall::create([
            'product_id' => 49,
            'retail_price' => 1500.00 * (2 / 5), // 600.00
            'cogs' => 1100.00 * (2 / 5), // 440.00
            'excluded_price' => 1100.00 * (2 / 5), // 440.00
        ]);

        // Product 50
        Product::create([
            'id' => 50,
            'name' => 'Supply & install Combo 2 In 1 Washer Dryer with IoT Enabled',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 50,
            'retail_price' => 0.00 * (3 / 5), // 0.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 50,
            'retail_price' => 0.00 * (2 / 5), // 0.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 51
        Product::create([
            'id' => 51,
            'name' => 'Supply and Install hood and hob',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 51,
            'retail_price' => 2000.00 * (3 / 5), // 1200.00
            'cogs' => 1500.00 * (3 / 5), // 900.00
            'excluded_price' => 1500.00 * (3 / 5), // 900.00
        ]);

        ProductInstall::create([
            'product_id' => 51,
            'retail_price' => 2000.00 * (2 / 5), // 800.00
            'cogs' => 1500.00 * (2 / 5), // 600.00
            'excluded_price' => 1500.00 * (2 / 5), // 600.00
        ]);

        // Product 52
        Product::create([
            'id' => 52,
            'name' => 'Supply & Install iBilikPlus IoT Enabled Smart Main Door Lock',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => 'with double latches',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 52,
            'retail_price' => 545.00 * (3 / 5), // 327.00
            'cogs' => 600.00 * (3 / 5), // 360.00
            'excluded_price' => 600.00 * (3 / 5), // 360.00
        ]);

        ProductInstall::create([
            'product_id' => 52,
            'retail_price' => 545.00 * (2 / 5), // 218.00
            'cogs' => 600.00 * (2 / 5), // 240.00
            'excluded_price' => 600.00 * (2 / 5), // 240.00
        ]);

        // Product 53
        Product::create([
            'id' => 53,
            'name' => 'Supply and install CCTV in dining area',
            'pm_category_id' => 3,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 53,
            'retail_price' => 250.00 * (3 / 5), // 150.00
            'cogs' => 180.00 * (3 / 5), // 108.00
            'excluded_price' => 180.00 * (3 / 5), // 108.00
        ]);

        ProductInstall::create([
            'product_id' => 53,
            'retail_price' => 250.00 * (2 / 5), // 100.00
            'cogs' => 180.00 * (2 / 5), // 72.00
            'excluded_price' => 180.00 * (2 / 5), // 72.00
        ]);

        // Product 54
        Product::create([
            'id' => 54,
            'name' => 'Microwave',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 1,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 54,
            'retail_price' => 400.00 * (3 / 5), // 240.00
            'cogs' => 230.00 * (3 / 5), // 138.00
            'excluded_price' => 230.00 * (3 / 5), // 138.00
        ]);

        ProductInstall::create([
            'product_id' => 54,
            'retail_price' => 400.00 * (2 / 5), // 160.00
            'cogs' => 230.00 * (2 / 5), // 92.00
            'excluded_price' => 230.00 * (2 / 5), // 92.00
        ]);

        // Product 55
        Product::create([
            'id' => 55,
            'name' => 'Hot & Warm Water Dispenser c/w 4 Layer Korea Technology Filtration',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 55,
            'retail_price' => 380.00 * (3 / 5), // 228.00
            'cogs' => 266.00 * (3 / 5), // 159.60
            'excluded_price' => 266.00 * (3 / 5), // 159.60
        ]);

        ProductInstall::create([
            'product_id' => 55,
            'retail_price' => 380.00 * (2 / 5), // 152.00
            'cogs' => 266.00 * (2 / 5), // 106.40
            'excluded_price' => 266.00 * (2 / 5), // 106.40
        ]);

        // Product 56
        Product::create([
            'id' => 56,
            'name' => '2 door mini bar Fridge',
            'pm_category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 56,
            'retail_price' => 500.00 * (3 / 5), // 300.00
            'cogs' => 400.00 * (3 / 5), // 240.00
            'excluded_price' => 400.00 * (3 / 5), // 240.00
        ]);

        ProductInstall::create([
            'product_id' => 56,
            'retail_price' => 500.00 * (2 / 5), // 200.00
            'cogs' => 400.00 * (2 / 5), // 160.00
            'excluded_price' => 400.00 * (2 / 5), // 160.00
        ]);


        // // ===================================================================
        // // ===================================================================
        // // ===================================================================

        // Product 57
        Product::create([
            'id' => 57,
            'name' => 'Supply and install 1 hp aircond without copper piping - midea/ gree/ hisense',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 4,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 57,
            'retail_price' => 1300.00 * (3 / 5), // 780.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 57,
            'retail_price' => 1300.00 * (2 / 5), // 520.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 58
        Product::create([
            'id' => 58,
            'name' => 'Relocation of aircond to the partitioned room',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 3,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 58,
            'retail_price' => 250.00 * (3 / 5), // 150.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 58,
            'retail_price' => 250.00 * (2 / 5), // 100.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);


        // // ===================================================================
        // // ===================================================================
        // // ===================================================================

        // Product 59
        Product::create([
            'id' => 59,
            'name' => 'Supply & Install iBilikPlus IoT Enabled Smart Room Door Lock',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 59,
            'retail_price' => 350.00 * (3 / 5), // 210.00
            'cogs' => 297.00 * (3 / 5), // 178.20
            'excluded_price' => 297.00 * (3 / 5), // 178.20
        ]);

        ProductInstall::create([
            'product_id' => 59,
            'retail_price' => 350.00 * (2 / 5), // 140.00
            'cogs' => 297.00 * (2 / 5), // 118.80
            'excluded_price' => 297.00 * (2 / 5), // 118.80
        ]);

        // Product 60
        Product::create([
            'id' => 60,
            'name' => 'Supply & Install iBilikPlus IoT Enabled Smart Meter connected to WHOLE room',
            'pm_category_id' => 3,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 5,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 60,
            'retail_price' => 500.00 * (3 / 5), // 300.00
            'cogs' => 199.00 * (3 / 5), // 119.40
            'excluded_price' => 199.00 * (3 / 5), // 119.40
        ]);

        ProductInstall::create([
            'product_id' => 60,
            'retail_price' => 500.00 * (2 / 5), // 200.00
            'cogs' => 199.00 * (2 / 5), // 79.60
            'excluded_price' => 199.00 * (2 / 5), // 79.60
        ]);

        // Product 61
        Product::create([
            'id' => 61,
            'name' => 'Supply & Install Smart WIFI G2 Gateway Hub',
            'pm_category_id' => 2,
            'type' => 'service',
            'status' => 'available',
            'task_weightage' => 2,
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 61,
            'retail_price' => 168.00 * (3 / 5), // 100.80
            'cogs' => 168.00 * (3 / 5), // 100.80
            'excluded_price' => 168.00 * (3 / 5), // 100.80
        ]);

        ProductInstall::create([
            'product_id' => 61,
            'retail_price' => 168.00 * (2 / 5), // 67.20
            'cogs' => 168.00 * (2 / 5), // 67.20
            'excluded_price' => 168.00 * (2 / 5), // 67.20
        ]);

        // Product 62
        Product::create([
            'id' => 62,
            'name' => 'Manpower cost for M&E AND Painting',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 62,
            'retail_price' => 700.00 * (3 / 5), // 420.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 62,
            'retail_price' => 700.00 * (2 / 5), // 280.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 63
        Product::create([
            'id' => 63,
            'name' => 'Roudup for Partition Queen-Sized Bedroom',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 63,
            'retail_price' => 920.00 * (3 / 5), // 552.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 63,
            'retail_price' => 920.00 * (2 / 5), // 368.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 64
        Product::create([
            'id' => 64,
            'name' => 'Roundup for Partition Single-Sized Bedroom',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 64,
            'retail_price' => 1090.00 * (3 / 5), // 654.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 64,
            'retail_price' => 1090.00 * (2 / 5), // 436.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 65
        Product::create([
            'id' => 65,
            'name' => 'Roundup for Queen-Sized Bedroom',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 65,
            'retail_price' => 920.00 * (3 / 5), // 552.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 65,
            'retail_price' => 920.00 * (2 / 5), // 368.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 66
        Product::create([
            'id' => 66,
            'name' => 'Roundup for Single-Sized Bedroom',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 66,
            'retail_price' => 890.00 * (3 / 5), // 534.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 66,
            'retail_price' => 890.00 * (2 / 5), // 356.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 67
        Product::create([
            'id' => 67,
            'name' => 'Roundup for Kitchen',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 67,
            'retail_price' => 320.00 * (3 / 5), // 192.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 67,
            'retail_price' => 320.00 * (2 / 5), // 128.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 68
        Product::create([
            'id' => 68,
            'name' => 'Roundup for Dining, Yard & Foyer',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 68,
            'retail_price' => 1610.00 * (3 / 5), // 966.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 68,
            'retail_price' => 1610.00 * (2 / 5), // 644.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
        ]);

        // Product 69
        Product::create([
            'id' => 69,
            'name' => 'Roundup for Commune Living Space',
            'pm_category_id' => 1,
            'type' => 'service',
            'status' => 'available',
            'description' => '',
            'uom' => 'unit',
        ]);

        ProductSupply::create([
            'product_id' => 69,
            'retail_price' => 3250.00 * (3 / 5), // 1950.00
            'cogs' => 0.00 * (3 / 5), // 0.00
            'excluded_price' => 0.00 * (3 / 5), // 0.00
        ]);

        ProductInstall::create([
            'product_id' => 69,
            'retail_price' => 3250.00 * (2 / 5), // 1300.00
            'cogs' => 0.00 * (2 / 5), // 0.00
            'excluded_price' => 0.00 * (2 / 5), // 0.00
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
    }
}
