<?php
/**
 * Test Adjust Stock Endpoint
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test Adjust Stock Endpoint ===\n\n";

try {
    // Check if route exists
    echo "1. Checking routes...\n";
    $routes = app('router')->getRoutes();
    $found = false;
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'products/{id}/adjust-stock') !== false) {
            echo "   ✓ Route found: " . $route->uri() . "\n";
            echo "   Method: " . implode(', ', $route->methods()) . "\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "   ✗ Route NOT found!\n";
    }
    echo "\n";
    
    // Check if ProductController exists
    echo "2. Checking ProductController...\n";
    if (class_exists('App\Http\Controllers\ProductController')) {
        echo "   ✓ ProductController exists\n";
        
        $controller = new App\Http\Controllers\ProductController();
        if (method_exists($controller, 'adjustStock')) {
            echo "   ✓ adjustStock method exists\n";
        } else {
            echo "   ✗ adjustStock method NOT found!\n";
        }
    } else {
        echo "   ✗ ProductController NOT found!\n";
    }
    echo "\n";
    
    // Check products table
    echo "3. Checking products in database...\n";
    $products = DB::table('products')->limit(3)->get();
    
    if ($products->isEmpty()) {
        echo "   ⚠ No products found in database\n";
    } else {
        echo "   ✓ Found " . $products->count() . " products:\n";
        foreach ($products as $product) {
            echo "   - ID: {$product->id}, Name: {$product->name_ar}, Stock: {$product->stock_quantity}\n";
        }
    }
    echo "\n";
    
    // Test the endpoint URL
    echo "4. Expected endpoint URL:\n";
    echo "   POST http://localhost/parfumes/backend/public/api/products/{id}/adjust-stock\n";
    echo "   Example: POST http://localhost/parfumes/backend/public/api/products/1/adjust-stock\n";
    echo "\n";
    
    echo "5. Required request body:\n";
    echo "   {\n";
    echo "     \"type\": \"in\" or \"out\" or \"adjustment\",\n";
    echo "     \"quantity\": 10,\n";
    echo "     \"notes\": \"Optional notes\"\n";
    echo "   }\n";
    echo "\n";
    
    echo "=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}
