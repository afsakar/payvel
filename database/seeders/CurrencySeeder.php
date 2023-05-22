<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Currency::insert([
            'name' => 'Turkish Lira',
            'code' => 'TRY',
            'position' => 'right',
            'symbol' => "₺",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \App\Models\Currency::insert([
            'name' => 'American Dollar',
            'code' => 'USD',
            'position' => 'left',
            'symbol' => "$",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \App\Models\Currency::insert([
            'name' => 'Euro',
            'code' => 'EUR',
            'position' => 'left',
            'symbol' => "€",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \App\Models\Currency::insert([
            'name' => 'Pound Sterling',
            'code' => 'GBP',
            'position' => 'left',
            'symbol' => "£",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
