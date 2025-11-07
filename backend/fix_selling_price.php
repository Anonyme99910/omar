<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Making selling_price nullable ===\n\n";

try {
    DB::statement('ALTER TABLE products MODIFY COLUMN selling_price DECIMAL(10,2) NULL');
    echo "✅ selling_price is now nullable\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
