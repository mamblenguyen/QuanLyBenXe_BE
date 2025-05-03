<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RouteStatus;

class RouteStatusSeeder extends Seeder
{
    public function run(): void
    {
        RouteStatus::insert([
            ['name' => 'Đang hoạt động'],
            ['name' => 'Tạm ngưng'],
            ['name' => 'Đã kết thúc'],
        ]);
    }
}
