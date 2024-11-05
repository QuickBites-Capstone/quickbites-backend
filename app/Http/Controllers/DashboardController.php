<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getDashboardStats(): JsonResponse
    {
        $totalOrders = Order::count();

        $totalCustomers = Cart::distinct('customer_id')->count('customer_id');

        $totalEarnings = Cart::sum('total');

        $data = [
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
            'total_earnings' => $totalEarnings,
        ];

        return response()->json($data);
    }

    public function getTopSellingItems(): JsonResponse
    {
        $topSellingItems = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', 'products.image', 'products.price', DB::raw('SUM(cart_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name', 'products.image', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return response()->json($topSellingItems);
    }
}
