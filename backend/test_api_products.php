<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== Testing /api/products Endpoint ===\n\n";

$products = Product::select(['id', 'name', 'name_ar', 'sku', 
                      'selling_price', 'price_جملة', 'price_قطاعي', 'price_صفحة',
                      'volume_ml', 'quantity', 'alert_quantity', 'photos', 'is_active'])
    ->orderBy('name_ar')
    ->limit(5)
    ->get();

echo "Total products in database: " . Product::count() . "\n\n";

if ($products->isEmpty()) {
    echo "❌ No products found!\n";
    echo "The products table is empty.\n";
    exit;
}

echo "✅ Found " . $products->count() . " products:\n\n";

foreach ($products as $product) {
    echo "---\n";
    echo "ID: {$product->id}\n";
    echo "Name (AR): {$product->name_ar}\n";
    echo "Name (EN): " . ($product->name ?? 'NULL') . "\n";
    echo "SKU: " . ($product->sku ?? 'NULL') . "\n";
    echo "Volume: {$product->volume_ml}\n";
    echo "Quantity: {$product->quantity}\n";
    echo "Alert: {$product->alert_quantity}\n";
    echo "Price جملة: {$product->price_جملة}\n";
    echo "Price قطاعي: {$product->price_قطاعي}\n";
    echo "Price صفحة: {$product->price_صفحة}\n";
    echo "Is Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
}

echo "\n✅ API should return these products to both Stock page and POS!\n";
