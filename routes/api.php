<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'getProductsByCategory']);
Route::get('/products/all', [ProductController::class, 'index']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::post('/products', [ProductController::class, 'store']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::get('cart/{customerId}', [CartController::class, 'getCart']); // Get the customer's cart
Route::post('cart/{customerId}', [CartController::class, 'store']); // Add items to the cart
Route::put('cart/{cartId}/items/{itemId}', [CartController::class, 'updateCartItem']); // Update cart item quantity
Route::delete('cart/{cartId}/items/{itemId}', [CartController::class, 'removeCartItem']); // Remove a cart item
Route::delete('cart/{cartId}', [CartController::class, 'clearCart']); // Clear all cart items


Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{categoryId}/products', [CategoryController::class, 'getProductsByCategory']);

Route::get('/customers', [CustomerController::class, 'search']);
Route::post('/customers/{id}/add-credits', [CustomerController::class, 'addCredits']);
Route::get('/customers/{id}', [CustomerController::class, 'getCustomerById']);

Route::post('/register', [CustomerController::class, 'register']);
Route::post('/login', [CustomerController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [CustomerController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/get-customer-name', [CustomerController::class, 'getCustomerName']);

Route::get('/reasons', [ReasonController::class, 'index']);

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/today', [OrderController::class, 'todayOrders']);
Route::post('/orders', [OrderController::class, 'store']);
Route::patch('/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);


Route::get('/roles', [RoleController::class, 'index']);
Route::get('/admins', [AdminController::class, 'index']);
Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/register', [AdminController::class, 'register']);
Route::middleware('auth:sanctum')->get('/get-admin-name', [AdminController::class, 'getAdminName']);
Route::middleware('auth:sanctum')->post('/admin/logout', [AdminController::class, 'logout']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
