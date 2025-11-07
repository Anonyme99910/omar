<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== Restoring Photos with Simple Placeholders ===\n\n";

// Create a simple SVG placeholder as data URL
function createSvgPlaceholder($color, $text) {
    $svg = <<<SVG
<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
  <rect width="400" height="400" fill="#{$color}"/>
  <text x="50%" y="50%" font-family="Arial" font-size="24" fill="white" text-anchor="middle" dominant-baseline="middle">{$text}</text>
</svg>
SVG;
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

// Color palette
$colors = [
    '3b82f6', // Blue
    '10b981', // Green
    'f59e0b', // Orange
    'ef4444', // Red
    '8b5cf6', // Purple
    '06b6d4', // Cyan
    'f43f5e', // Rose
    '84cc16', // Lime
];

$products = Product::all();

echo "Found " . $products->count() . " products.\n\n";

$count = 0;
foreach ($products as $product) {
    $colorIndex = $count % count($colors);
    $color = $colors[$colorIndex];
    $text = substr($product->name_ar, 0, 10); // First 10 chars of Arabic name
    
    // Create SVG data URL
    $photoUrl = createSvgPlaceholder($color, $text);
    
    // Update product photo
    $product->photos = json_encode([$photoUrl]);
    $product->save();
    
    echo "✅ Added photo for: {$product->name_ar}\n";
    $count++;
}

echo "\n✅ Added photos for {$count} products!\n";
echo "Photos are now stored as inline SVG data URLs (no external requests).\n";
