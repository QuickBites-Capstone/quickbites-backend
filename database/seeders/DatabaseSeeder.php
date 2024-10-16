<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Charles',
                'last_name' => 'Casenas',
                'email' => 'charles@gmail.com',
                'password' => bcrypt('gwapo123'),
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Seed Customers
        DB::table('customers')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@gmail.com',
                'password' => bcrypt('gwapo123'),
                'balance' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Seed Categories
        DB::table('categories')->insert([
            ['name' => 'Meals', 'image' => 'storage/image.jpg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Snacks', 'image' => 'storage/image.jpg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Rice',
                'image' => 'storage/image.jpg',
                'price' => 10,
                'stock_quantity' => 0,
                'status_id' => 2,
                'category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Seed Cart Items
        DB::table('cart_items')->insert([
            [
                'cart_id' => 1,
                'product_id' => 1,
                'quantity' => 50,
                'price' => 1500,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cart_id' => 1,
                'product_id' => 2,
                'quantity' => 25,
                'price' => 250,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}