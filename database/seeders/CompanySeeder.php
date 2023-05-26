<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Company::insert([
            'name' => 'Example Company',
            'owner' => 'John Doe',
            'tel_number' => '1234567890',
        ]);
    }
}
