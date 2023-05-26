<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::insert([
            'name' => 'Food',
            'type' => 'expense',
            'created_at' => now(),
        ]);

        \App\Models\Category::insert([
            'name' => 'Salary',
            'type' => 'income',
            'created_at' => now(),
        ]);

        \App\Models\Category::insert([
            'name' => 'Rent',
            'type' => 'expense',
            'created_at' => now(),
        ]);

        \App\Models\Category::insert([
            'name' => 'Transportation',
            'type' => 'expense',
            'created_at' => now(),
        ]);

        \App\Models\Category::insert([
            'name' => 'Entertainment',
            'type' => 'expense',
            'created_at' => now(),
        ]);

        \App\Models\Category::insert([
            'name' => 'Shopping',
            'type' => 'expense',
            'created_at' => now(),
        ]);

        \App\Models\Category::insert([
            'name' => 'Travel',
            'type' => 'expense',
            'created_at' => now(),
        ]);

        \App\Models\Category::insert([
            'name' => 'Health',
            'type' => 'expense',
            'created_at' => now(),
        ]);
    }
}
