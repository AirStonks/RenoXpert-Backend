<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test staff user
        User::updateOrCreate(
            ['email' => 'staff@belive.asia'],
            [
                'name' => 'Staff User',
                'email' => 'staff@belive.asia',
                'password' => Hash::make('nQT6A6MKkWDAUj5V'),
                'type' => 'staff',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        echo "✓ Staff user created: staff@belive.asia\n";

        // Create admin user (optional)
        User::updateOrCreate(
            ['email' => 'admin@renoxpert.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@renoxpert.com',
                'password' => Hash::make('admin123'),
                'type' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        echo "✓ Admin user created: admin@renoxpert.com\n";
    }
}

