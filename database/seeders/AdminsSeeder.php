<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('admins')->insert([
            [
                'first_name' => 'Joshua Paulo',
                'last_name' => 'Ynzon',
                'email' => 'joshuapauloynzon@gmail.com',
                'password' => bcrypt('gwapo123'),
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'first_name' => 'Charles',
                'last_name' => 'Casenas',
                'email' => 'casenascharles@gmail.com',
                'password' => bcrypt('gwapo123'),
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'first_name' => 'Canteen',
                'last_name' => 'Staff',
                'email' => 'canteenstaff@quickbites.com',
                'password' => bcrypt('gwapo123'),
                'role_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
