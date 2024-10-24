<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_status')->insert([
            ['name' => 'Available'],
            ['name' => 'Sold Out'],
        ]);
    }
}