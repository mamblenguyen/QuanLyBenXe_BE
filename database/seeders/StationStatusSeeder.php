<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('station_statuses')->insert([
            ['name' => 'Đang hoạt động'],
            ['name' => 'Tạm dừng'],
            ['name' => 'Đóng cửa'],
        ]);
    }
}
