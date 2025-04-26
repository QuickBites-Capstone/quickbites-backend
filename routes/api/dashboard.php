<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats']);
Route::get('/dashboard/top-selling-items', [DashboardController::class, 'getTopSellingItems']);
Route::get('/dashboard/monthly-earnings', [DashboardController::class, 'getMonthlyEarnings']);
