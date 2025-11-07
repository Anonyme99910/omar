<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;

echo "=== Testing Product API Endpoint ===\n\n";

try {
    $controller = new ProductController();
    $request = new Request();
    
    echo "Testing GET /api/products...\n";
    $response = $controller->index($request);
    $data = json_decode($response->getContent(), true);
    
    echo "✅ API Response successful!\n";
    echo "Total products: " . count($data['data']) . "\n";
    
    if (count($data['data']) > 0) {
        echo "\nFirst product:\n";
        $first = $data['data'][0];
        echo "- ID: {$first['id']}\n";
        echo "- Name: {$first['name_ar']}\n";
        echo "- Volume: {$first['volume_ml']}\n";
        echo "- Quantity: {$first['quantity']}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
