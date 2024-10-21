<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Set the current timestamp
        $now = Carbon::now();

        // Seed Roles
        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'staff'],
        ]);

        // Seed Admins
        DB::table('admins')->insert([
            [
                'first_name' => 'Joshua',
                'last_name' => 'Paulo',
                'email' => 'joshua@gmail.com',
                'password' => bcrypt('gwapo123'), // Hash the password
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

        // Seed Customers
        DB::table('customers')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'profile_picture' => 'https://img.freepik.com/premium-vector/people-saving-money_24908-51569.jpg?semt=ais_hybrid',
                'email' => 'john@gmail.com',
                'phone_number' => '091234567',
                'password' => bcrypt('gwapo123'),
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Seed Categories
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


        // Seed Product Status
        DB::table('product_status')->insert([
            ['name' => 'Available'],
            ['name' => 'Sold Out'],
        ]);

        // Seed Products
        DB::table('products')->insert([
            [
                'name' => 'Fried chicken',
                'image' => 'storage/image.jpg',
                'price' => 30,
                'stock_quantity' => 100,
                'status_id' => 1,
                'category_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Rice',
                'image' => 'storage/image.jpg',
                'price' => 10,
                'stock_quantity' => 0,
                'status_id' => 2,
                'category_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Seed Payment Methods
        DB::table('payment_methods')->insert([
            ['name' => 'Wallet'],
            ['name' => 'Cash'],
        ]);

        // Seed Carts
        DB::table('carts')->insert([
            [
                'customer_id' => 1,
                'total' => 1750,
                'schedule' => '14:20:00',
                'payment_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Seed Cart Items
        DB::table('cart_items')->insert([
            [
                'cart_id' => 1,
                'product_id' => 1,
                'quantity' => 50,
                'price' => 1500,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'cart_id' => 1,
                'product_id' => 2,
                'quantity' => 25,
                'price' => 250,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Seed Order Status
        DB::table('order_status')->insert([
            ['name' => 'Pending'],
            ['name' => 'In progress'],
            ['name' => 'Ready for pick-up'],
            ['name' => 'Complete'],
            ['name' => 'Cancelled'],
        ]);

        // Seed Reasons
        DB::table('reasons')->insert([
            ['description' => 'Too many orders'],
            ['description' => 'This is spam'],
        ]);

        // Seed Orders
        DB::table('orders')->insert([
            [
                'cart_id' => 1,
                'order_status_id' => 1,
                'reason_id' => null, // or a valid reason id if applicable
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}