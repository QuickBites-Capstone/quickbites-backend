<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Carbon\Carbon;
use App\Http\Services\ImageService;
use App\Models\Product;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    
    public function __construct(protected ImageService $imageService, protected DashboardService $dashboardService)
    {
        $this->imageService = $imageService;
        $this->dashboardService = $dashboardService;
    }

    public function getDashboardStats(): JsonResponse
    {
        $data = $this->dashboardService->getStats();

        return response()->json($data);
    }

    public function getTopSellingItems(): JsonResponse
    {
        $topSellingItems = $this->dashboardService->getTopSellingItems();

        return response()->json($topSellingItems);
    }

    public function getMonthlyEarnings(): JsonResponse
    {
        $monthlyEarnings = $this->dashboardService->getMonthlyEarnings();

        return response()->json(['monthly_earnings' => $monthlyEarnings]);
    }
}