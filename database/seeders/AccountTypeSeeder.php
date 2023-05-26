<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AccountType::insert([
            'name' => 'Bank Account',
            'created_at' => now(),
        ]);

        \App\Models\AccountType::insert([
            'name' => 'Credit Card',
            'created_at' => now(),
        ]);
    }
}
