<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\AdminRoleMiddleware;


Route::get('/roles', [RoleController::class, 'index']);
Route::get('/admins', [AdminController::class, 'index']);
Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function () {
    Route::post('/admin/register', [AdminController::class, 'register']);
    Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
});
Route::post('/admin/login', [AdminController::class, 'login']);
Route::middleware('auth:sanctum')->get('/get-admin-name', [AdminController::class, 'getAdminName']);
Route::middleware('auth:sanctum')->post('/admin/logout', [AdminController::class, 'logout']);