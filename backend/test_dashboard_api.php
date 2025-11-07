<?php
/**
 * Dashboard API Test & Debug Script
 * Tests the dashboard endpoint and checks database
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Dashboard API Test & Debug ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "   ✓ Database connected successfully\n\n";
    
    // Check if tables exist
    echo "2. Checking tables...\n";
    $tables = ['sales', 'products', 'customers', 'sale_items'];
    foreach ($tables as $table) {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        echo "   " . ($exists ? "✓" : "✗") . " Table '$table' " . ($exists ? "exists" : "MISSING") . "\n";
    }
    echo "\n";
    
    // Check sales data
    echo "3. Checking sales data...\n";
    $totalSales = DB::table('sales')->count();
    $paidSales = DB::table('sales')->whereIn('status', ['paid', 'partially_paid'])->count();
    $todaySales = DB::table('sales')
        ->whereDate('created_at', today())
        ->whereIn('status', ['paid', 'partially_paid'])
        ->count();
    
    echo "   Total sales: $totalSales\n";
    echo "   Paid/Partially paid sales: $paidSales\n";
    echo "   Today's paid sales: $todaySales\n\n";
    
    // Check products
    echo "4. Checking products...\n";
    $totalProducts = DB::table('products')->count();
    echo "   Total products: $totalProducts\n\n";
    
    // Check customers
    echo "5. Checking customers...\n";
    $totalCustomers = DB::table('customers')->count();
    echo "   Total customers: $totalCustomers\n\n";
    
    // Test dashboard query
    echo "6. Testing dashboard query...\n";
    $today = today();
    $thisMonth = now()->startOfMonth();
    
    $todaySalesAmount = DB::table('sales')
        ->whereDate('created_at', $today)
        ->whereIn('status', ['paid', 'partially_paid'])
        ->sum('total');
    
    $monthSalesAmount = DB::table('sales')
        ->where('created_at', '>=', $thisMonth)
        ->whereIn('status', ['paid', 'partially_paid'])
        ->sum('total');
    
    echo "   Today's sales amount: EGP " . number_format($todaySalesAmount, 2) . "\n";
    echo "   This month's sales amount: EGP " . number_format($monthSalesAmount, 2) . "\n\n";
    
    // Show sample sales
    echo "7. Sample sales records:\n";
    $sampleSales = DB::table('sales')
        ->select('id', 'invoice_number', 'total', 'status', 'created_at')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    if ($sampleSales->isEmpty()) {
        echo "   ⚠ No sales records found!\n";
        echo "   This is why dashboard shows EGP 0.00\n\n";
        
        echo "8. Checking if we need to seed data...\n";
        echo "   Run: php artisan db:seed\n";
        echo "   Or create some test sales through the POS system\n\n";
    } else {
        foreach ($sampleSales as $sale) {
            echo "   - Invoice: {$sale->invoice_number}, Total: EGP {$sale->total}, Status: {$sale->status}, Date: {$sale->created_at}\n";
        }
        echo "\n";
    }
    
    // Test API endpoint directly
    echo "9. Testing API endpoint...\n";
    echo "   URL: http://localhost/parfumes/backend/public/api/reports/dashboard\n";
    echo "   Method: GET\n";
    echo "   Headers: Authorization: Bearer {token}\n\n";
    
    echo "=== Summary ===\n";
    if ($totalSales == 0) {
        echo "❌ ISSUE FOUND: No sales data in database\n";
        echo "   Solution: Create sales through POS or seed database\n";
    } else if ($paidSales == 0) {
        echo "❌ ISSUE FOUND: No paid/partially_paid sales\n";
        echo "   Solution: Make sure sales have status='paid' or 'partially_paid'\n";
    } else {
        echo "✓ Database has sales data\n";
        echo "   Month sales amount: EGP " . number_format($monthSalesAmount, 2) . "\n";
        echo "   If dashboard still shows 0.00, check:\n";
        echo "   - API authentication (token)\n";
        echo "   - Browser console for errors\n";
        echo "   - Network tab for API response\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
