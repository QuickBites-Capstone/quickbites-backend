<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            AdminsSeeder::class,
            CategoriesSeeder::class,
            ProductStatusSeeder::class,
            PaymentMethodsSeeder::class,
            OrderStatusSeeder::class,
            ReasonsSeeder::class,
        ]);
    }
}