<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== Checking Recent Products ===\n\n";

// Get all products ordered by most recent
$products = Product::orderBy('created_at', 'desc')->limit(10)->get();

echo "Last 10 products created:\n\n";

foreach ($products as $product) {
    echo "ID: {$product->id}\n";
    echo "Name: {$product->name_ar}\n";
    echo "Volume: {$product->volume_ml}\n";
    echo "Quantity: {$product->quantity}\n";
    echo "Photos: " . ($product->photos ? json_encode($product->photos) : 'NULL') . "\n";
    echo "Created: {$product->created_at}\n";
    echo "---\n";
}

// Check for 'tes' specifically
echo "\n=== Searching for 'tes' ===\n\n";
$tesProducts = Product::where('name_ar', 'like', '%tes%')
    ->orWhere('name', 'like', '%tes%')
    ->get();

if ($tesProducts->count() > 0) {
    echo "Found {$tesProducts->count()} product(s) matching 'tes':\n\n";
    foreach ($tesProducts as $product) {
        echo "ID: {$product->id} - {$product->name_ar}\n";
    }
} else {
    echo "❌ No products found matching 'tes'\n";
}

// Total count
$total = Product::count();
echo "\n=== Total Products: {$total} ===\n";
