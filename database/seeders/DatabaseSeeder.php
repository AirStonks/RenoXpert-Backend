<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
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
            'name' => 'Kitchen',
            'description' => 'This is kitchen',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Bedroom',
            'description' => 'This is bedroom',
        ]);

        ProductCategory::create([
            'name' => 'Bathroom',
            'description' => 'This is bathroom',
        ]);

        Product::create([
            'name' => 'Curtain (MB)',
            'category_id' => 3,
            'SKU' => '981234745678',
            'type' => 'component',
            'status' => 'available',
            'price' => 100.00,
            'description' => 'Curtain for medium bedroom',
        ]);

        Product::create([
            'name' => 'Bedroom Wiring',
            'category_id' => 3,
            'SKU' => '186723545825',
            'type' => 'service',
            'status' => 'available',
            'price' => 150.00,
            'description' => 'Electric, cable wiring service',
        ]);
    }
}
