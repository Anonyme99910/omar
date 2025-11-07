<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== Updating Mock Photos to Local Paths ===\n\n";

// Create a simple placeholder image generator function
function createPlaceholderImage($color, $text, $filename) {
    $width = 400;
    $height = 400;
    
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bgColor = imagecolorallocate($image, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
    $textColor = imagecolorallocate($image, 255, 255, 255);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Add text
    $fontSize = 5;
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    imagestring($image, $fontSize, $x, $y, $text, $textColor);
    
    // Save image
    imagejpeg($image, $filename, 90);
    imagedestroy($image);
}

// Create uploads directory if it doesn't exist
$uploadsDir = __DIR__ . '/public/uploads/products';
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
    echo "Created uploads directory\n";
}

// Color palette
$colors = [
    '3b82f6', // Blue
    '10b981', // Green
    'f59e0b', // Orange
    'ef4444', // Red
    '8b5cf6', // Purple
];

$products = Product::all();

echo "Found " . $products->count() . " products.\n\n";

$count = 0;
foreach ($products as $product) {
    $colorIndex = $count % count($colors);
    $color = $colors[$colorIndex];
    $text = "Product " . ($count + 1);
    
    $filename = 'placeholder_' . $product->id . '.jpg';
    $filepath = $uploadsDir . '/' . $filename;
    
    // Create placeholder image
    createPlaceholderImage($color, $text, $filepath);
    
    // Update product photo path
    $product->photos = json_encode(['/uploads/products/' . $filename]);
    $product->save();
    
    echo "✅ Updated photo for: {$product->name_ar} -> {$filename}\n";
    $count++;
}

echo "\n✅ Updated photos for {$count} products!\n";
echo "All photos are now stored locally in: {$uploadsDir}\n";
