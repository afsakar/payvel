<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Account::insert([
            'account_type_id' => 1, // 'Bank Account
            'currency_id' => 1, // 'TRY'
            'name' => 'Example Bank Account',
            'starting_balance' => 0,
            'created_at' => now(),
        ]);
    }
}
