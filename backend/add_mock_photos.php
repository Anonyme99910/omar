<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== Adding Mock Photos to Products ===\n\n";

// Placeholder image URL (using a free placeholder service)
$placeholderPhotos = [
    'https://via.placeholder.com/400x400/3b82f6/ffffff?text=Perfume+1',
    'https://via.placeholder.com/400x400/10b981/ffffff?text=Perfume+2',
    'https://via.placeholder.com/400x400/f59e0b/ffffff?text=Perfume+3',
    'https://via.placeholder.com/400x400/ef4444/ffffff?text=Perfume+4',
    'https://via.placeholder.com/400x400/8b5cf6/ffffff?text=Perfume+5',
];

$products = Product::whereNull('photos')->orWhere('photos', '')->get();

echo "Found " . $products->count() . " products without photos.\n\n";

$count = 0;
foreach ($products as $product) {
    // Assign a random placeholder
    $photoUrl = $placeholderPhotos[$count % count($placeholderPhotos)];
    
    $product->photos = json_encode([$photoUrl]);
    $product->save();
    
    echo "✅ Added photo to: {$product->name_ar}\n";
    $count++;
}

echo "\n✅ Added mock photos to {$count} products!\n";
echo "\nNote: These are placeholder images. Upload real product photos through the admin panel.\n";
