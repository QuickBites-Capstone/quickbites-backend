<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Carbon\Carbon;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getDashboardStats(): JsonResponse
    {
        $totalOrders = Order::count();
        $totalCustomers = Cart::distinct('customer_id')->count('customer_id');
        $totalEarnings = Cart::sum('total');

        $dailyEarnings = Cart::whereDate('created_at', Carbon::today())->sum('total');

        $data = [
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
            'total_earnings' => $totalEarnings,
            'daily_earnings' => $dailyEarnings,
        ];

        return response()->json($data);
    }

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getTopSellingItems(): JsonResponse
    {
        $topSellingItems = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.image',
                'products.price',
                DB::raw('SUM(cart_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.image', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $topSellingItems->transform(function ($item) {
            $item->image_url = $this->imageService->getTemporaryImageUrl($item->image);
            return $item;
        });

        return response()->json($topSellingItems);
    }

    public function getMonthlyEarnings(): JsonResponse
    {
        $monthlyEarnings = DB::table('orders')
            ->join('carts', 'orders.cart_id', '=', 'carts.id')
            ->selectRaw('EXTRACT(MONTH FROM orders.created_at) AS month, SUM(carts.total) AS earnings')
            ->groupBy(DB::raw('EXTRACT(MONTH FROM orders.created_at)'))
            ->orderBy(DB::raw('EXTRACT(MONTH FROM orders.created_at)'))
            ->pluck('earnings', 'month')
            ->toArray();

        $earningsPerMonth = array_fill(1, 12, 0);
        foreach ($monthlyEarnings as $month => $earnings) {
            $earningsPerMonth[$month] = $earnings;
        }

        return response()->json(['monthly_earnings' => array_values($earningsPerMonth)]);
    }
}