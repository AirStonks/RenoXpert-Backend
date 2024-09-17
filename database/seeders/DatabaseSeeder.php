<?php

namespace Database\Seeders;

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
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);

        ProductCategory::create([
            'name' => 'Foyer',
            'description' => 'This is foyer',
        ]);
    }
}
