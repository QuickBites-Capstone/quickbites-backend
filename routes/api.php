<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'getProductsByCategory']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/customers', [CustomerController::class, 'search']);
Route::post('/register', [CustomerController::class, 'register']);
Route::post('/login', [CustomerController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [CustomerController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/get-customer-name', [CustomerController::class, 'getCustomerName']);

Route::get('/reasons', [ReasonController::class, 'index']);

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/today', [OrderController::class, 'todayOrders']);
Route::post('/orders', [OrderController::class, 'store']);



Route::get('/roles', [RoleController::class, 'index']);
Route::get('/admins', [AdminController::class, 'index']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/register', [AdminController::class, 'register']);
Route::middleware('auth:sanctum')->get('/get-admin-name', [AdminController::class, 'getAdminName']);
Route::middleware('auth:sanctum')->post('/admin/logout', [AdminController::class, 'logout']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
