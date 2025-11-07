<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;

echo "=== Testing Sales API ===\n\n";

// Create mock request
$request = new Request();
$request->merge([
    'start_date' => '2025-11-01',
    'end_date' => '2025-11-01'
]);

echo "Request params:\n";
echo "  start_date: " . $request->start_date . "\n";
echo "  end_date: " . $request->end_date . "\n\n";

// Call controller
$controller = new SaleController();
$response = $controller->index($request);
$data = json_decode($response->getContent(), true);

echo "Response structure:\n";
echo "  Has 'data' key: " . (isset($data['data']) ? 'YES' : 'NO') . "\n";
echo "  Has 'meta' key: " . (isset($data['meta']) ? 'YES' : 'NO') . "\n";
echo "  Has 'counts' key: " . (isset($data['counts']) ? 'YES' : 'NO') . "\n\n";

if (isset($data['data'])) {
    echo "Sales count: " . count($data['data']) . "\n\n";
    
    if (count($data['data']) > 0) {
        $firstSale = $data['data'][0];
        echo "First sale structure:\n";
        echo "  ID: " . ($firstSale['id'] ?? 'missing') . "\n";
        echo "  Total: " . ($firstSale['total'] ?? 'missing') . "\n";
        echo "  Profit: " . ($firstSale['profit'] ?? 'missing') . "\n";
        echo "  Items Count: " . ($firstSale['items_count'] ?? 'missing') . "\n";
        echo "  Customer: " . ($firstSale['customer']['name'] ?? 'missing') . "\n";
        echo "  Items loaded: " . (isset($firstSale['items']) ? count($firstSale['items']) : 0) . "\n";
    }
}

echo "\n=== Full Response (first sale) ===\n";
if (isset($data['data'][0])) {
    echo json_encode($data['data'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
