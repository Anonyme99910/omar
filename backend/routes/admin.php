<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
| All routes here require authentication and admin privileges
*/

Route::middleware(['auth:sanctum', App\Http\Middleware\AdminMiddleware::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/statistics/category', [AdminController::class, 'statisticsByCategory']);
    Route::get('/statistics/status', [AdminController::class, 'statisticsByStatus']);
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users']);
    Route::put('/users/{userId}/toggle-status', [AdminController::class, 'toggleUserStatus']);
    
    // Properties Management
    Route::get('/properties', [AdminController::class, 'properties']);
    Route::put('/properties/{propertyId}/status', [AdminController::class, 'updatePropertyStatus']);
    Route::delete('/properties/{propertyId}', [AdminController::class, 'deleteProperty']);
});
