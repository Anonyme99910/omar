<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DamagedProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ProfileController;

// Test route
Route::get('/', function () {
    return response()->json([
        'message' => 'Perfume Store API',
        'version' => '1.0',
        'status' => 'running'
    ]);
});

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// Public PDF download (no auth required for customer access)
Route::get('/invoice/{invoice_number}', [SaleController::class, 'publicPdfDownload']);

// Public API endpoints (for POS and testing)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::get('/sales', [SaleController::class, 'index']);
Route::post('/sales', [SaleController::class, 'store']);
Route::get('/sales/{id}/pdf', [SaleController::class, 'downloadPdf']);
Route::get('/damaged-products', [DamagedProductController::class, 'index']);

// Packages (public for POS)
Route::get('/packages', [PackageController::class, 'index']);
Route::post('/packages', [PackageController::class, 'store']);
Route::get('/packages/{id}', [PackageController::class, 'show']);
Route::put('/packages/{id}', [PackageController::class, 'update']);
Route::delete('/packages/{id}', [PackageController::class, 'destroy']);

// Expenses
Route::get('/expenses/statistics', [ExpenseController::class, 'statistics']);
Route::get('/expenses', [ExpenseController::class, 'index']);
Route::post('/expenses', [ExpenseController::class, 'store']);
Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

// Expense Types
Route::get('/expense-types', [ExpenseTypeController::class, 'index']);
Route::post('/expense-types', [ExpenseTypeController::class, 'store']);
Route::put('/expense-types/{id}', [ExpenseTypeController::class, 'update']);
Route::delete('/expense-types/{id}', [ExpenseTypeController::class, 'destroy']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Categories (full CRUD)
    Route::apiResource('categories', CategoryController::class)->except(['index']);

    // Brands
    Route::post('brands', [BrandController::class, 'store']);
    Route::put('brands/{id}', [BrandController::class, 'update']);
    Route::delete('brands/{id}', [BrandController::class, 'destroy']);

    // Products (additional protected routes)
    Route::get('products/barcode/{barcode}', [ProductController::class, 'searchByBarcode']);
    Route::get('products/low-stock/list', [ProductController::class, 'lowStock']);
    Route::post('products/{id}/adjust-stock', [ProductController::class, 'adjustStock']);

    // Customers (additional protected routes)
    Route::put('customers/{id}', [CustomerController::class, 'update']);
    Route::delete('customers/{id}', [CustomerController::class, 'destroy']);
    Route::get('customers/{id}/history', [CustomerController::class, 'history']);

    // Sales / Invoices (additional protected routes)
    Route::get('sales/{id}', [SaleController::class, 'show']);
    Route::post('sales/{id}/cancel', [SaleController::class, 'cancel']);
    Route::post('sales/{id}/void', [SaleController::class, 'void']);
    Route::get('sales/today/summary', [SaleController::class, 'todaySales']);
    Route::get('sales/{id}/whatsapp', [SaleController::class, 'getWhatsAppMessage']);
    
    // Payments
    Route::get('sales/{id}/payments', [PaymentController::class, 'index']);
    Route::post('sales/{id}/payments', [PaymentController::class, 'store']);
    
    // Stock Management
    Route::get('stock', [StockController::class, 'index']);
    Route::get('stock/movements', [StockController::class, 'movements']);
    Route::post('stock/adjust', [StockController::class, 'adjust']);
    Route::get('stock/low-stock', [StockController::class, 'lowStock']);

    // Damaged Products
    Route::get('damaged-products', [DamagedProductController::class, 'index']);
    Route::post('damaged-products', [DamagedProductController::class, 'store']);
    Route::get('damaged-products/stats', [DamagedProductController::class, 'stats']);
    Route::delete('damaged-products/{id}', [DamagedProductController::class, 'destroy']);

    // Reports
    Route::get('reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('reports/sales', [ReportController::class, 'salesReport']);
    Route::get('reports/products', [ReportController::class, 'productReport']);
    Route::get('reports/inventory', [ReportController::class, 'inventoryReport']);
    Route::get('reports/profit', [ReportController::class, 'profitReport']);
    
    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::put('employees/{id}/permissions', [EmployeeController::class, 'updatePermissions']);
    
    // Roles
    Route::get('roles', [RoleController::class, 'index']);
    
    // Profile
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
});
