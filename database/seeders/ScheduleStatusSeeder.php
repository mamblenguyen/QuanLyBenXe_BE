<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('schedule_statuses')->insert([
            ['name' => 'Chưa khởi hành'],
            ['name' => 'Đã xuất phát'],
            ['name' => 'Tạm ngừng'],
        ]);
    }
}
