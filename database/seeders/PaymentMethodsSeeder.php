<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        DB::table('payment_methods')->insert([
            ['name' => 'Wallet'],
            ['name' => 'Cash'],
        ]);
    }
}