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
            'name' => 'Tire İnşaat',
            'owner' => 'Muzaffer ORUÇ',
            'tel_number' => '4122245630',
        ]);

        \App\Models\Company::insert([
            'name' => 'Madalyon Mühendislik İnşaat',
            'owner' => 'Mehmet Ali ORUÇ',
            'tel_number' => '4122245630',
        ]);
    }
}
