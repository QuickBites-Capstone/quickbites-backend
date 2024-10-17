<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;

Route::get('/admins', [AdminController::class, 'index']);
Route::get('/roles', [RoleController::class, 'index']);

Route::get('/orders', [OrderController::class, 'index']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
