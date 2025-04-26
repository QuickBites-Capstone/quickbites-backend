<?php

namespace App\Services;

use App\Http\Services\ImageService;
use App\Models\Order;
use App\Repositories\DashboardRepository;
use App\Repositories\OrderRepository;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DashboardRepository $dashboardRepository, protected ImageService $imageService,
        protected OrderRepository $orderRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
        $this->imageService = $imageService;
        $this->orderRepository = $orderRepository;
    }

    public function getStats(): array
    {
        return [
            'total_orders' => $this->dashboardRepository->getTotalOrders(),
            'total_customers' => $this->dashboardRepository->getTotalCustomers(),
            'total_earnings' => $this->dashboardRepository->getTotalEarnings(),
            'daily_earnings' => $this->dashboardRepository->getDailyEarnings(),
        ];
    }

    public function getTopSellingItems(): array
    {
        $products = $this->dashboardRepository->getTopSellingItems();

        return $products->transform(function ($product) {
            $product->image_url = $this->imageService->getTemporaryImageUrl($product->image);
            return $product;
        })->toArray();
    }

    public function getMonthlyEarnings(): array
    {
        $orders = $this->orderRepository->getMonthlyOrdersWithCart();

        $earningsPerMonth = array_fill(1, 12, 0);

        foreach ($orders as $month => $orderGroup) {
            $earningsPerMonth[$month] = $orderGroup->sum(function ($order) {
                return $order->cart->total ?? 0;
            });
        }

        return array_values($earningsPerMonth);
    }
}
