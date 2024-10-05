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
        ]);

        ProductCategory::create([
            'name' => 'Bedroom',
            'description' => 'This is Bedroom',
        ]);

        ProductCategory::create([
            'name' => 'Dining, Yard & Foyer',
            'description' => 'This is Dining, Yard & Foyer',
        ]);

        ProductCategory::create([
            'name' => 'Commune Living Space',
            'description' => 'This is Commune Living Space',
        ]);

        ProductCategory::create([
            'name' => 'Toilet Furnishing (Freebies)',
            'description' => 'This is Toilet Furnishing',
        ]);

        Product::create([
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
            'name' => 'Accent Wall - Designer-look painting',
            'category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 300.00,
            'product_cost_of_good_sold' => 150.00,
            'product_excluded_price' => 0.00,
            'description' => '',
        ]);

        Product::create([
            'name' => 'Built-In Single-sized Bedhead & Bedframe',
            'category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 400.00,
            'product_cost_of_good_sold' => 280.00,
            'product_excluded_price' => 380.00,
            'description' => 'with 2nos Soft-Close System Drawers, Fabricated w/ LED strip & 13A plugpoint',
        ]);

        Product::create([
            'name' => 'Built-In 2 Doors Swing Wardrobe',
            'category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 600.00,
            'product_cost_of_good_sold' => 290.00,
            'product_excluded_price' => 480.00,
            'description' => 'with full height mirror (1200mm (W) x 2400mm (H) x 480mm (D),Fabricated w/ LED strip & 2nos 13A plugpoints',
        ]);

        Product::create([
            'name' => 'Built-In Study Table Set',
            'category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 300.00,
            'product_cost_of_good_sold' => 230.00,
            'product_excluded_price' => 250.00,
            'description' => '750mm (W) x 750mm (H) x 480mm (D) Fabricated w/ 13A plugpoints',
        ]);

        Product::create([
            'name' => 'Built-In Wall-Mounted Cabinet Unit',
            'category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 200.00,
            'product_cost_of_good_sold' => 0.00,
            'product_excluded_price' => 130.00,
            'description' => 'Fabricated w/ LED Strip',
        ]);

        Product::create([
            'name' => 'Goodnite Branded - 10" Single-sized mattress with 10 years warranty',
            'category_id' => 2,
            'type' => 'component',
            'status' => 'available',
            'product_retail_price' => 800.00,
            'product_cost_of_good_sold' => 350.00,
            'product_excluded_price' => 638.00,
            'description' => 'Damask Fabric w/ Posture Spring System, Non-Flip Tech',
        ]);

        $package1 = Package::create([
            'name' => 'Partition Queen-Sized Bedroom',
            'description' => 'This is the Partition Queen-Sized Bedroom',
            'total_price' => 3150.00
        ]);

        $package1->products()->attach(1, ['quantity' => 1]);
        $package1->products()->attach(2, ['quantity' => 1]);
        $package1->products()->attach(3, ['quantity' => 1]);
        $package1->products()->attach(4, ['quantity' => 1]);
        $package1->products()->attach(5, ['quantity' => 1]);
        $package1->products()->attach(6, ['quantity' => 1]);
        $package1->products()->attach(7, ['quantity' => 1]);

        $package2 = Package::create([
            'name' => 'Partition Single-Sized Bedroom',
            'description' => 'This is the Partition Single-Sized Bedroom',
            'total_price' => 2600.00
        ]);

        $package2->products()->attach(8, ['quantity' => 1]);
        $package2->products()->attach(9, ['quantity' => 1]);
        $package2->products()->attach(10, ['quantity' => 1]);
        $package2->products()->attach(11, ['quantity' => 1]);
        $package2->products()->attach(12, ['quantity' => 1]);
        $package2->products()->attach(13, ['quantity' => 1]);

        Contact::create([
            'name' => 'Alex Chong',
            'email' => 'alexchong55@gmail.com',
            'phone_no' => '0123456789',
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
