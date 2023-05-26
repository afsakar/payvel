<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CorporationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Corporation::insert([
            'currency_id' => 1, // 'TRY
            'type' => 'customer', // 'company
            'name' => 'Example Corporation',
            'tax_number' => '1234567890',
            'tax_office' => 'Example Tax Office',
            'address' => 'Example Address',
            'tel_number' => '1234567890',
            'email' => 'john@example.com',
            'created_at' => now(),
        ]);
    }
}
