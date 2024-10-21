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
            // Meals
            ['name' => 'Fried Chicken', 'image' => 'https://img.freepik.com/free-photo/close-up-fried-chicken-drumsticks_23-2148682835.jpg', 'price' => 30, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pork Adobo', 'image' => 'https://img.freepik.com/premium-photo/adobo-white-plate-topped-with-chicken-rice-generative-ai-image-philippines-food_87646-14564.jpg', 'price' => 35, 'stock_quantity' => 80, 'status_id' => 1, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Beef Stir-Fry', 'image' => 'https://img.freepik.com/premium-photo/bowl-beef-stir-fry-with-sesame-seeds-top-it_787273-1761.jpg', 'price' => 40, 'stock_quantity' => 50, 'status_id' => 1, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grilled Fish', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3vJZaF6akeHbnbE7hikgp7HihZBbv2W0uqw&s', 'price' => 32, 'stock_quantity' => 60, 'status_id' => 1, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vegetable Curry', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTEoOuKovZIkixusqQpp9DF5kXiHAM5vrU8SA&s', 'price' => 25, 'stock_quantity' => 90, 'status_id' => 1, 'category_id' => 1, 'created_at' => $now, 'updated_at' => $now],

            // Snacks
            ['name' => 'Banana Queue', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTdS9pb6WjO_3p3O0mM4wzU9eKltcL8seVMkA&s', 'price' => 15, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            [
                'name' => 'Pancakes',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLJRg2kQzeCUCANZOKsPX2YYJbZxgALmHEnA&s',
                'price' => 20,
                'stock_quantity' => 80,
                'status_id' => 1,
                'category_id' => 2,
                'created_at' => $now,
                'updated_at' => $now
            ],
            ['name' => 'Cheese Sticks', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSjOUsQiS0PDk7oIQXwIx_UfABWJqg74my_xA&s', 'price' => 18, 'stock_quantity' => 70, 'status_id' => 1, 'category_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Spring Rolls', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcThlwlArNVD5aivEhhSVSnezD8yEcmTe8YSZg&s', 'price' => 22, 'stock_quantity' => 60, 'status_id' => 1, 'category_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fruit Cups', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRp7nZSLpxJZ9azkDeLoyneXfhHaTS0r9J88Q&s', 'price' => 10, 'stock_quantity' => 90, 'status_id' => 1, 'category_id' => 2, 'created_at' => $now, 'updated_at' => $now],

            // Chips
            ['name' => 'Potato Chips', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzxOk3XsTMza8PCDs6SF2efYSQpDZ8iOMVgg&s', 'price' => 25, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cheddar Cheese Puffs', 'image' => 'https://www.cheetos.com/sites/cheetos.com/files/2019-03/SimpleCheetosWhiteCheddar_v2.png', 'price' => 30, 'stock_quantity' => 80, 'status_id' => 1, 'category_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Barbecue Corn Chips', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQax1Jg2gn_O8UQLyig4KHUHt4YVehfxDR97Q&s', 'price' => 28, 'stock_quantity' => 70, 'status_id' => 1, 'category_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tortilla Chips', 'image' => 'https://manilabambifoods.com/cdn/shop/products/CDT_TortillaChips500gplainNEW_1024x1024.jpg?v=1655967957', 'price' => 20, 'stock_quantity' => 90, 'status_id' => 1, 'category_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Veggie Chips', 'image' => 'https://m.media-amazon.com/images/I/81vz-USEpOL._SL1500_.jpg', 'price' => 35, 'stock_quantity' => 60, 'status_id' => 1, 'category_id' => 3, 'created_at' => $now, 'updated_at' => $now],

            // Candies
            ['name' => 'Chocolate Bars', 'image' => 'https://i5.walmartimages.com/seo/Cadbury-Dairy-Milk-Chocolate-Bar-180G_a166c967-1247-4d48-84e0-a543118c98c3.49f1302f0b151093e6cb2489ea75dd95.jpeg', 'price' => 15, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gummy Bears', 'image' => 'https://ever.ph/cdn/shop/files/9000005682-Trolli-Classic-Bears-Gummy-Candy-100g-230306_9c1bfe8c-b89a-4e60-b717-593ad02528b9_300x300.jpg?v=1725261347', 'price' => 12, 'stock_quantity' => 80, 'status_id' => 1, 'category_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hard Candies', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0jG8pUp10i-r2Lb50dBTXZmL1i0xwSDP2GA&s', 'price' => 10, 'stock_quantity' => 90, 'status_id' => 1, 'category_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chocolate-Covered Nuts', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSPh9p-hSYK8rrymhsE8EzS8ry9-zZDKnk9mw&s', 'price' => 20, 'stock_quantity' => 70, 'status_id' => 1, 'category_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lollipops', 'image' => 'https://ever.ph/cdn/shop/files/100000063557-Chupa-Chups-Assorted-Flavour-Lollipops-12g-210311_0ffff058-fd76-4d5c-8036-c3ed410a000d_300x300.jpg?v=1725256814', 'price' => 5, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 4, 'created_at' => $now, 'updated_at' => $now],

            // Drinks
            ['name' => 'Coca-Cola', 'image' => 'https://ever.ph/cdn/shop/files/9000002359-Coke-Coca-Cola-Original-Taste-Can-320ml-230206_19ce8c26-7fba-4a2d-a72d-7cb6a278987e_1200x1200.jpg?v=1727831441', 'price' => 20, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sprite', 'image' => 'https://www.contis.ph/cdn/shop/products/SpriteinCan.jpg?v=1689558530', 'price' => 20, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fruit Juice (Apple)', 'image' => 'https://fdi.com.ph/wp-content/uploads/2020/07/Ceres-apple-1L-600x600.jpg', 'price' => 25, 'stock_quantity' => 80, 'status_id' => 1, 'category_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chocolate Milk', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6Pa-k4l117Gswec9pmP_sCxj7Zh4TsKL1jw&s', 'price' => 30, 'stock_quantity' => 70, 'status_id' => 1, 'category_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Water Bottle', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRke5vUCY2gDkkrLTCYfhddvl7am3X7MqdAng&s', 'price' => 10, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 5, 'created_at' => $now, 'updated_at' => $now],

            // Supplies
            ['name' => 'ID Holder', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSe-86KYf5hmQYj5dTSTz5yM4eHKckFqM34nQ&s', 'price' => 15, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pencil Case', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyDizrbFjIp0h_LJdZd4iUxOkhscXaT0WHUQ&s', 'price' => 25, 'stock_quantity' => 80, 'status_id' => 1, 'category_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            [
                'name' => 'Notebook',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGONqCbKxH7kDjGFp3EbH6W6tWXYX40Ef7cA&s',
                'price' => 30,
                'stock_quantity' => 60,
                'status_id' => 1,
                'category_id' => 6,
                'created_at' => $now,
                'updated_at' => $now
            ],
            ['name' => 'Ballpoint Pens', 'image' => 'https://smstationery.com.ph/cdn/shop/products/10000197-PilotBPSBallPenFine0.7mm.png?v=1646880806', 'price' => 10, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Erasers', 'image' => 'storage/erasers.jpg', 'price' => 5, 'stock_quantity' => 100, 'status_id' => 1, 'category_id' => 6, 'created_at' => $now, 'updated_at' => $now],
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