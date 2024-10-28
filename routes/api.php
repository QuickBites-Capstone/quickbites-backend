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
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\AdminRoleMiddleware;

Route::get('/products', [ProductController::class, 'getProductsByCategory']);
Route::get('/products/all', [ProductController::class, 'index']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::post('/products', [ProductController::class, 'store']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::get('cart/{customerId}', [CartController::class, 'getCart']);
Route::post('cart/{customerId}', [CartController::class, 'store']);
Route::put('cart/{cartId}/items/{itemId}', [CartController::class, 'updateCartItem']);
Route::delete('cart/{cartId}/items/{itemId}', [CartController::class, 'removeCartItem']);
Route::delete('cart/{cartId}', [CartController::class, 'clearCart']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{categoryId}/products', [CategoryController::class, 'getProductsByCategory']);

Route::get('/customers', [CustomerController::class, 'search']);
Route::post('/customers/{id}/add-credits', [CustomerController::class, 'addCredits']);
Route::get('/customers/{id}', [CustomerController::class, 'getCustomerBalanceById']);
Route::put('customer/{customerId}/balance', [CustomerController::class, 'updateBalance']);
Route::post('/register', [CustomerController::class, 'register']);
Route::post('/login', [CustomerController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [CustomerController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/get-customer-name', [CustomerController::class, 'getCustomerName']);
Route::middleware('auth:sanctum')->get('/customers/balance', [CustomerController::class, 'getCustomerBalance']);
Route::middleware('auth:sanctum')->put('/update-customer', [CustomerController::class, 'updateCustomer']);
Route::middleware('auth:sanctum')->post('/update-profile-picture', [CustomerController::class, 'updateProfilePicture']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/send-otp', [CustomerController::class, 'sendOtpForPasswordChange']);
    Route::post('/change-password', [CustomerController::class, 'changePassword']);
    Route::post('/verify-otp', [CustomerController::class, 'verifyOtp']);
});

Route::get('/reasons', [ReasonController::class, 'index']);

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/today', [OrderController::class, 'todayOrders']);
Route::post('/orders', [OrderController::class, 'store']);
Route::patch('/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);
Route::get('/customers/{customerId}/orders/inactive', [OrderController::class, 'getOrdersWithInactiveCart']);


Route::get('/roles', [RoleController::class, 'index']);
Route::get('/admins', [AdminController::class, 'index']);
Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function(){
    Route::post('/admin/register', [AdminController::class, 'register']);
    Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
});
Route::post('/admin/login', [AdminController::class, 'login']);
Route::middleware('auth:sanctum')->get('/get-admin-name', [AdminController::class, 'getAdminName']);
Route::middleware('auth:sanctum')->post('/admin/logout', [AdminController::class, 'logout']);

// Routes for managing notifications
Route::middleware('auth:sanctum')->group(function () {
    // Get all notifications for a specific user
    Route::get('/notifications', [NotificationController::class, 'index']);

    // Store a new notification
    Route::post('/notifications', [NotificationController::class, 'store']);

    // Get a specific notification
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);

    // Update a notification
    Route::put('/notifications/{id}', [NotificationController::class, 'update']);

    // Delete a notification
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});

Route::post('/send-message', [MessageController::class, 'sendMessage']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');