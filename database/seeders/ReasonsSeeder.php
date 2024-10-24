<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonsSeeder extends Seeder
{
    public function run()
    {
        DB::table('reasons')->insert([
            ['description' => 'Canteen is very busy at the moment!'],
            ['description' => 'This is a doulbe order'],
            ['description' => 'This is spam'],
        ]);
    }
}