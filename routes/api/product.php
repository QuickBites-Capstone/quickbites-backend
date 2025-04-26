<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


Route::get('/search-products', [ProductController::class, 'searchProduct']);
Route::get('/products', [ProductController::class, 'getProductsByCategory']);
Route::get('/products/all', [ProductController::class, 'index']);
Route::put('/update-product/{id}', [ProductController::class, 'update']);
Route::post('/store-product', [ProductController::class, 'store']);
Route::delete('/product/{id}', [ProductController::class, 'destroy']);