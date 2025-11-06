<?php

namespace Database\Seeders;

use App\Models\Finance\Sale;
use App\Models\Foundation\User;
use App\Models\Business\Order;
use App\Models\Foundation\Address;
use App\Models\Foundation\Contact;
use App\Models\Finance\Invoice;
use App\Models\Catalog\Package;
use App\Models\Finance\Payment;
use App\Models\Catalog\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Property\Property;
use App\Models\Business\Quotation;
use App\Models\PMCategory;
use App\Models\Catalog\ProductSupply;
use App\Models\Business\OrderQuotation;
use App\Models\Catalog\ProductInstall;
use Illuminate\Database\Seeder;
use App\Models\Lead\RegistrationForm;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
    }
}