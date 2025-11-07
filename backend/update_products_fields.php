<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Updating Products Table ===\n\n";

try {
    // Change volume_ml to VARCHAR for manual input
    DB::statement("ALTER TABLE products MODIFY COLUMN volume_ml VARCHAR(50) NULL");
    echo "✅ volume_ml changed to VARCHAR(50)\n";
    
    // Make name nullable
    DB::statement("ALTER TABLE products MODIFY COLUMN name VARCHAR(255) NULL");
    echo "✅ name is now nullable\n";
    
    // Make sku nullable and remove unique constraint if needed
    DB::statement("ALTER TABLE products MODIFY COLUMN sku VARCHAR(255) NULL");
    echo "✅ sku is now nullable\n";
    
    echo "\n✅ All changes applied successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
