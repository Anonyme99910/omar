<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== Testing Product Creation ===\n\n";

try {
    $product = Product::create([
        'name_ar' => 'منتج تجريبي',
        'volume_ml' => '100 مل',
        'price_جملة' => 85.00,
        'price_قطاعي' => 100.00,
        'price_صفحة' => 110.00,
        'quantity' => 50,
        'alert_quantity' => 10,
    ]);
    
    echo "✅ Product created successfully!\n";
    echo "ID: {$product->id}\n";
    echo "Name: {$product->name_ar}\n";
    echo "Volume: {$product->volume_ml}\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
