<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            ['name' => 'Xe giường nằm 45 chổ', 'capacity' => 45,
             'created_at' => \Illuminate\Support\Carbon::now(),
             'updated_at' => \Illuminate\Support\Carbon::now(),],
            ['name' => 'Xe giường nằm 34 chổ', 'capacity' => 34,
              'created_at' => \Illuminate\Support\Carbon::now(),
              'updated_at' => \Illuminate\Support\Carbon::now(),],
            ['name' => 'Xe giường nằm VIP 22 phòng', 'capacity' => 22,
             'created_at' => \Illuminate\Support\Carbon::now(),
             'updated_at' => \Illuminate\Support\Carbon::now(),],

        ];

        DB::table('bus_models')->insert($models);
    }
}
