<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BusStationSeeder extends Seeder
{
    public function run()
    {
        $garageIds = DB::table('garages')->pluck('id')->toArray();

        DB::table('bus_stations')->insert([
            [
                'name' => 'Bến xe Cần Thơ',
                'garage_id' => $garageIds[array_rand($garageIds)],
                'province_id' => 59,
                'status_id' => 1,
                'address' => 'Số 91B Đường Lê Hồng Phong, Ninh Kiều',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bến xe An Giang',
                'garage_id' => $garageIds[array_rand($garageIds)],
                'province_id' => 57,
                'status_id' => 1,
                'address' => 'Số 12 Đường Trần Hưng Đạo, Long Xuyên',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bến xe Miền Đông',
                'garage_id' => $garageIds[array_rand($garageIds)],
                'province_id' => 50,
                'status_id' => 1,
                'address' => 'Số 292 Đinh Bộ Lĩnh, Bình Thạnh',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bến xe Đà Lạt',
                'garage_id' => $garageIds[array_rand($garageIds)],
                'province_id' => 44,
                'status_id' => 1,
                'address' => 'Số 1 Đường Tô Hiệu, Đà Lạt',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bến xe Vĩnh Long',
                'garage_id' => $garageIds[array_rand($garageIds)],
                'province_id' => 55,
                'status_id' => 1,
                'address' => 'Số 43 Đường Phạm Hùng, Vĩnh Long',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
