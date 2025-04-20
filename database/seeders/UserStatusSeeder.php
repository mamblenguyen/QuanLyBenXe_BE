<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_statuses')->insert([
            ['name' => 'Đang hoạt động'],
            ['name' => 'Tạm dừng'],
            ['name' => 'Đã khóa'],
        ]);
    }
}
