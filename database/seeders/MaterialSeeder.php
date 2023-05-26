<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Material::insert([
            'unit_id' => 1, // 'Kilogram
            'tax_id' => 1, // 'VAT (Value Added Tax) - 18%
            'currency_id' => 1, // 'TRY
            'name' => 'Example Material',
            'code' => 'MTR0001',
            'price' => 20,
            'category' => 'construction',
            'type' => 'procurement',
            'description' => 'Example Description',
            'created_at' => now(),
        ]);

        \App\Models\Material::insert([
            'unit_id' => 1, // 'Kilogram
            'tax_id' => 1, // 'VAT (Value Added Tax) - 18%
            'currency_id' => 1, // 'TRY
            'name' => 'Example Material 2',
            'code' => 'MTR0002',
            'price' => 30,
            'category' => 'construction',
            'type' => 'procurement',
            'description' => 'Example Description 2',
            'created_at' => now(),
        ]);
    }
}
