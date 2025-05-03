<?php

// database/seeders/RouteSeeder.php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RouteSchedule;
class RouteSeeder extends Seeder
{
    public function run()
    {
        $stations = DB::table('bus_stations')->pluck('id')->toArray();
        $buses = DB::table('buses')->pluck('id')->toArray();
        $drivers = DB::table('users')->pluck('id')->toArray();

        for ($i = 0; $i < 3; $i++) {
            $departure = $stations[array_rand($stations)];
            $arrival = $stations[array_rand($stations)];
            while ($arrival == $departure) {
                $arrival = $stations[array_rand($stations)];
            }

            $route = Route::create([
                'departure_station_id' => $departure,
                'arrival_station_id' => $arrival,
                'garage_id' => 1,
                'bus_id' => $buses[array_rand($buses)],
                'driver_id' => $drivers[array_rand($drivers)],
            ]);

            // Tạo nhiều khung giờ cho 1 tuyến
            $schedules = [
                ['08:00:00', '12:00:00', 100000],
                ['09:00:00', '13:00:00', 120000],
                ['10:00:00', '14:00:00', 150000],
            ];

            foreach ($schedules as [$depart, $arrive, $price]) {
                RouteSchedule::create([
                    'route_id' => $route->id,
                    'departure_time' => $depart,
                    'arrival_time' => $arrive,
                    'ticket_price' => $price,
                ]);
            }
        }
    }
}

