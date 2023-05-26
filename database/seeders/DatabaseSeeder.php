<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        //\App\Models\User::factory()->create([
        //    'name' => 'John Doe',
        //    'email' => 'john@example.com',
        //]);

        $this->call([
            CurrencySeeder::class,
            CompanySeeder::class,
            AccountTypeSeeder::class,
            AccountSeeder::class,
            CategorySeeder::class,
            TaxSeeder::class,
            WithHoldingSeeder::class,
            UnitSeeder::class,
            CorporationSeeder::class,
            MaterialSeeder::class,
        ]);
    }
}
