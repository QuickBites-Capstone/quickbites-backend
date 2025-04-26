<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    /**
     * Create a new class instance.
     */
    public function getTotalOrders(): int
    {
        return Order::count();
    }

    public function getTotalCustomers(): int
    {
        return Cart::distinct('customer_id')->count('customer_id');
    }

    public function getTotalEarnings(): float
    {
        return Cart::sum('total');
    }

    public function getDailyEarnings(): float
    {
        return Cart::whereDate('created_at', Carbon::today())->sum('total');
    }

    public function getTopSellingItems(int $limit = 5)
    {
        return Product::withCount([
            'cartItems as total_sold' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }
        ])
        ->having('total_sold', '>', 0)
        ->orderByDesc('total_sold')
        ->take($limit)
        ->get();

    }
}
