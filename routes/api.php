<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\CustomerController;

Route::get('/admins', [AdminController::class, 'index']);
Route::get('/roles', [RoleController::class, 'index']);

Route::get('/category', [CategoryController::class, 'index']);

Route::get('/customers', [CustomerController::class, 'search']);
Route::post('/register', [CustomerController::class, 'register']);
Route::post('/login', [CustomerController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [CustomerController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/get-customer-name', [CustomerController::class, 'getCustomerName']);

Route::get('/reasons', [ReasonController::class, 'index']);

Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');