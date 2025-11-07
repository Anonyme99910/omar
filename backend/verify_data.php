<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Database Inventory Check ===\n\n";
echo "Categories: " . App\Models\Category::count() . "\n";
echo "Brands: " . App\Models\Brand::count() . "\n";
echo "Products: " . App\Models\Product::count() . "\n\n";

echo "=== Sample Products ===\n";
$products = App\Models\Product::with(['category', 'brand'])->limit(5)->get();
foreach ($products as $product) {
    echo sprintf(
        "- %s (%s) | Brand: %s | Stock: %d | Price: %.2f SAR\n",
        $product->name,
        $product->name_ar,
        $product->brand->name,
        $product->stock_quantity,
        $product->selling_price
    );
}

echo "\n=== Stock Summary ===\n";
echo "Total Stock Value: " . number_format(App\Models\Product::sum(\DB::raw('stock_quantity * selling_price')), 2) . " SAR\n";
echo "Low Stock Items: " . App\Models\Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count() . "\n";
