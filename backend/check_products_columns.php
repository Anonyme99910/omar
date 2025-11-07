<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Products Table Structure ===\n\n";

$columns = DB::select("DESCRIBE products");

echo "Current columns in products table:\n\n";
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type}) " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

echo "\n=== Checking for problematic columns ===\n\n";

$problematicColumns = ['stock_quantity', 'min_stock_level', 'reserved_qty', 'category_id', 'brand_id', 'cost_price', 'barcode'];

foreach ($problematicColumns as $col) {
    $exists = false;
    foreach ($columns as $column) {
        if ($column->Field === $col) {
            $exists = true;
            break;
        }
    }
    echo ($exists ? '❌' : '✅') . " {$col}: " . ($exists ? 'EXISTS (should be removed)' : 'Not found (good)') . "\n";
}
