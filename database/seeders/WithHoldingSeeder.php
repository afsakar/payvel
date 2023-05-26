<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WithHoldingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\WithHolding::insert([
            'name' => 'WithHolding 1',
            'rate' => 20,
            'created_at' => now(),
        ]);

        \App\Models\WithHolding::insert([
            'name' => 'WithHolding 2',
            'rate' => 35,
            'created_at' => now(),
        ]);
    }
}
