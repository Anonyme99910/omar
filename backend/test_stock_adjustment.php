<?php
/**
 * Test Stock Adjustment with Real API Call
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Product;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test Stock Adjustment ===\n\n";

try {
    // Get a product
    $product = Product::first();
    
    if (!$product) {
        echo "❌ No products found in database\n";
        exit(1);
    }
    
    echo "Testing with product:\n";
    echo "  ID: {$product->id}\n";
    echo "  Name: {$product->name_ar}\n";
    echo "  Current Stock: {$product->stock_quantity}\n\n";
    
    // Test the controller method directly
    $controller = new App\Http\Controllers\ProductController();
    
    // Create a mock request
    $request = new Illuminate\Http\Request();
    $request->merge([
        'type' => 'out',
        'quantity' => 1,
        'notes' => 'Test adjustment'
    ]);
    
    echo "Calling adjustStock with:\n";
    echo "  type: out\n";
    echo "  quantity: 1\n";
    echo "  notes: Test adjustment\n\n";
    
    $response = $controller->adjustStock($request, $product->id);
    $data = json_decode($response->getContent(), true);
    $statusCode = $response->getStatusCode();
    
    echo "Response Status: {$statusCode}\n";
    echo "Response Data:\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    if ($statusCode === 200) {
        echo "✅ Stock adjustment successful!\n";
        echo "  Previous Stock: {$data['previous_stock']}\n";
        echo "  New Stock: {$data['new_stock']}\n";
    } else {
        echo "❌ Stock adjustment failed!\n";
        if (isset($data['errors'])) {
            echo "Validation Errors:\n";
            foreach ($data['errors'] as $field => $errors) {
                echo "  {$field}: " . implode(', ', $errors) . "\n";
            }
        }
    }
    
    // Check inventory movements
    echo "\nChecking inventory_movements table:\n";
    $movements = DB::table('inventory_movements')
        ->where('product_id', $product->id)
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    if ($movements->isEmpty()) {
        echo "  ⚠ No movements found\n";
    } else {
        foreach ($movements as $movement) {
            echo "  - Type: {$movement->type}, Qty: {$movement->quantity}, Stock: {$movement->previous_stock} → {$movement->new_stock}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
