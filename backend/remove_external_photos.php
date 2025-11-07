<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== Removing External Photo URLs ===\n\n";

$products = Product::all();

echo "Found " . $products->count() . " products.\n\n";

$count = 0;
foreach ($products as $product) {
    // Set photos to null or empty array
    $product->photos = null;
    $product->save();
    
    echo "✅ Cleared photo for: {$product->name_ar}\n";
    $count++;
}

echo "\n✅ Cleared photos for {$count} products!\n";
echo "Products will now show default package icon in POS.\n";
