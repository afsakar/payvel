<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Tax::insert([
            'name' => 'VAT (Value Added Tax) - 18%',
            'rate' => 18,
            'created_at' => now(),
        ]);

        \App\Models\Tax::insert([
            'name' => 'GST (Goods and Services Tax) - 10%',
            'rate' => 10,
            'created_at' => now(),
        ]);
    }
}
