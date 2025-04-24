<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserStatusSeeder::class,
            GarageSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            RoleUserSeeder::class,
            BusModelSeeder::class,
            StationStatusSeeder::class,
            BusStatusSeeder::class,
            BusSeeder::class,
            BusStationSeeder::class,
            ScheduleStatusSeeder::class,
            DriverSeeder::class,
            RouteStatusSeeder::class,
            RouteSeeder::class,
            TicketSeeder::class
        ]);
    }
}
