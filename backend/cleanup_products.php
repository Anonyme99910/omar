<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Cleaning up remaining columns ===\n\n";

try {
    // Drop foreign key constraints first
    try {
        DB::statement("ALTER TABLE products DROP FOREIGN KEY products_category_id_foreign");
        echo "✓ Dropped category_id foreign key\n";
    } catch (\Exception $e) {
        echo "⚠ No category_id foreign key\n";
    }
    
    try {
        DB::statement("ALTER TABLE products DROP FOREIGN KEY products_brand_id_foreign");
        echo "✓ Dropped brand_id foreign key\n";
    } catch (\Exception $e) {
        echo "⚠ No brand_id foreign key\n";
    }
    
    // Now drop the columns
    DB::statement("ALTER TABLE products DROP COLUMN category_id");
    echo "✓ Dropped category_id\n";
    
    DB::statement("ALTER TABLE products DROP COLUMN brand_id");
    echo "✓ Dropped brand_id\n";
    
    echo "\n✅ Cleanup complete!\n\n";
    
    // Show final structure
    $columns = DB::select('SHOW COLUMNS FROM products');
    echo "Final columns:\n";
    foreach ($columns as $col) {
        echo "- {$col->Field}\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
