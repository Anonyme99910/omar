<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Dashboard API ===\n\n";

try {
    $controller = new App\Http\Controllers\ReportController();
    $response = $controller->dashboard();
    $data = json_decode($response->getContent(), true);
    
    echo "✅ Dashboard API working!\n\n";
    echo "Today's Sales: " . number_format($data['today']['sales'], 2) . " EGP\n";
    echo "Today's Orders: " . $data['today']['orders'] . "\n\n";
    echo "Month Sales: " . number_format($data['month']['sales'], 2) . " EGP\n";
    echo "Month Orders: " . $data['month']['orders'] . "\n\n";
    echo "Total Products: " . $data['inventory']['total_products'] . "\n";
    echo "Low Stock: " . $data['inventory']['low_stock'] . "\n";
    echo "Out of Stock: " . $data['inventory']['out_of_stock'] . "\n";
    echo "Inventory Value: " . number_format($data['inventory']['total_value'], 2) . " EGP\n";
    
    echo "\n✅ All dashboard data loaded successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
