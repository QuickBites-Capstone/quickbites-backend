<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('order_status')->insert([
            ['name' => 'Pending'],
            ['name' => 'In progress'],
            ['name' => 'Ready for pick-up'],
            ['name' => 'Complete'],
            ['name' => 'Cancelled'],
        ]);
    }
}