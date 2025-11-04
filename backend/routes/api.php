<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API Welcome route
Route::get('/', function () {
    return response()->json([
        'message' => 'Parfumes API',
        'version' => '1.0.0',
        'status' => 'running',
        'endpoints' => [
            'auth' => '/api/login, /api/register',
            'properties' => '/api/properties',
            'admin' => '/api/admin/dashboard'
        ]
    ]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public property routes
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);
    
    // Property routes
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    Route::get('/user/properties', [PropertyController::class, 'userProperties']);
    
    // Favorite routes
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{propertyId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{propertyId}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/{propertyId}/check', [FavoriteController::class, 'check']);
    
    // Upload routes
    Route::post('/upload', [UploadController::class, 'upload']);
    Route::post('/upload/multiple', [UploadController::class, 'uploadMultiple']);
    Route::delete('/upload', [UploadController::class, 'delete']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
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
