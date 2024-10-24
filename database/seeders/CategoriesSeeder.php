<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            [
                'name' => 'Meals',
                'image' => 'https://cdn-icons-png.freepik.com/512/7997/7997145.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Snacks',
                'image' => 'https://cdn-icons-png.freepik.com/512/5814/5814149.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Chips',
                'image' => 'https://cdn-icons-png.freepik.com/512/2575/2575818.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Candies',
                'image' => 'https://cdn-icons-png.freepik.com/512/1888/1888859.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Drinks',
                'image' => 'https://cdn-icons-png.freepik.com/256/783/783065.png?semt=ais_hybrid',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Supplies',
                'image' => 'https://cdn-icons-png.freepik.com/256/1025/1025801.png?semt=ais_hybrid',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}