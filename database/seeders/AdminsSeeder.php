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
                'first_name' => 'Joshua',
                'last_name' => 'Paulo',
                'email' => 'joshua@gmail.com',
                'password' => bcrypt('gwapo123'),
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'first_name' => 'Charles',
                'last_name' => 'Casenas',
                'email' => 'charles@gmail.com',
                'password' => bcrypt('gwapo123'),
                'role_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}