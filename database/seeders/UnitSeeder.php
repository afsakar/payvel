<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Unit::insert([
            'name' => 'Kilogram',
            'created_at' => now(),
        ]);

        \App\Models\Unit::insert([
            'name' => 'Gram',
            'created_at' => now(),
        ]);

        \App\Models\Unit::insert([
            'name' => 'Piece',
            'created_at' => now(),
        ]);
    }
}
