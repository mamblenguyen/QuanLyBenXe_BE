<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bus_statuses')->insert([
            ['name' => 'Đang hoạt động'],
            ['name' => 'Đang sửa chữa'],
            ['name' => 'Tạm ngừng'],
        ]);
    }
}
