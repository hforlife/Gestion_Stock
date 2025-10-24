<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\InventoryFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //    'name' => 'User1',
        //    'email' => 'user@example.com',
        //    'password'=> bcrypt('password'),
        // ]);

        // Item::factory(10)->create();
        // Inventory::factory(10)->create();
        // Customer::factory(10)->create();
        // PaymentMethod::factory(30)->create();

    }
}
