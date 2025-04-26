<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    /**
     * Create a new class instance.
     */
    public function getMonthlyOrdersWithCart()
    {
        return Order::with('cart')
            ->selectRaw('MONTH(created_at) as month')
            ->get()
            ->groupBy('month');
    }
}
