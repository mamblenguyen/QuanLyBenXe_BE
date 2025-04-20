<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        $garages = DB::table('garages')->pluck('id')->toArray();

        DB::table('users')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'User 1',
                'email' => 'user1@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '0123456789',
                'address' => 'Address 1',
                'birthday' => Carbon::create('1990', '01', '01'),
                'gender' => 'Nam',
                'avatar' => 'avatars/user1.png',
                'identity_card' => '123456789012',
                'identity_issue_place' => 'Hà Nội',
                'identity_issue_date' => Carbon::create('2010', '05', '01'),
                'status_id' => 1,
                'license' => 'AB123456',
                'license_issue_date' => Carbon::create('2015', '03', '01'),
                'license_expiry_date' => Carbon::create('2025', '03', '01'),
                'garage_id' => $garages[array_rand($garages)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'User 2',
                'email' => 'user2@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '0987654321',
                'address' => 'Address 2',
                'birthday' => Carbon::create('1985', '08', '15'),
                'gender' => 'Nữ',
                'avatar' => 'avatars/user2.png',
                'identity_card' => '987654321098',
                'identity_issue_place' => 'Hồ Chí Minh',
                'identity_issue_date' => Carbon::create('2012', '06', '20'),
                'status_id' => 2,
                'license' => 'CD987654',
                'license_issue_date' => Carbon::create('2017', '07', '10'),
                'license_expiry_date' => Carbon::create('2027', '07', '10'),
                'garage_id' => $garages[array_rand($garages)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'User 3',
                'email' => 'user3@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '0123987654',
                'address' => 'Address 3',
                'birthday' => Carbon::create('2000', '02', '25'),
                'gender' => 'Khác',
                'avatar' => 'avatars/user3.png',
                'identity_card' => '112233445566',
                'identity_issue_place' => 'Đà Nẵng',
                'identity_issue_date' => Carbon::create('2015', '09', '12'),
                'status_id' => 1,
                'license' => 'EF123456',
                'license_issue_date' => Carbon::create('2020', '02', '01'),
                'license_expiry_date' => Carbon::create('2030', '02', '01'),
                'garage_id' => $garages[array_rand($garages)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'User 4',
                'email' => 'user4@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '0909090909',
                'address' => 'Address 4',
                'birthday' => Carbon::create('1995', '04', '10'),
                'gender' => 'Nữ',
                'avatar' => 'avatars/user4.png',
                'identity_card' => '223344556677',
                'identity_issue_place' => 'Hải Phòng',
                'identity_issue_date' => Carbon::create('2018', '03', '05'),
                'status_id' => 3,
                'license' => 'GH123456',
                'license_issue_date' => Carbon::create('2021', '12', '15'),
                'license_expiry_date' => Carbon::create('2031', '12', '15'),
                'garage_id' => $garages[array_rand($garages)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'User 5',
                'email' => 'user5@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '0808080808',
                'address' => 'Address 5',
                'birthday' => Carbon::create('1980', '11', '30'),
                'gender' => 'Nam',
                'avatar' => 'avatars/user5.png',
                'identity_card' => '334455667788',
                'identity_issue_place' => 'Quảng Ninh',
                'identity_issue_date' => Carbon::create('2011', '04', '25'),
                'status_id' => 1,
                'license' => 'IJ123456',
                'license_issue_date' => Carbon::create('2016', '11', '20'),
                'license_expiry_date' => Carbon::create('2026', '11', '20'),
                'garage_id' => $garages[array_rand($garages)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
