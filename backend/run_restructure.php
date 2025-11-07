<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Starting Products Table Restructure ===\n\n";

try {
    // Step 1: Set segment prices
    echo "Step 1: Setting segment prices...\n";
    $updated = DB::statement("
        UPDATE products SET 
          price_جملة = ROUND(selling_price * 0.85, 2),
          price_قطاعي = selling_price,
          price_صفحة = ROUND(selling_price * 1.1, 2)
        WHERE price_جملة = 0
    ");
    echo "✓ Segment prices updated\n\n";

    // Step 2: Set random volumes
    echo "Step 2: Setting random volumes...\n";
    DB::statement("
        UPDATE products 
        SET volume_ml = ELT(FLOOR(1 + RAND() * 4), 50, 100, 150, 200)
        WHERE volume_ml = 100
    ");
    echo "✓ Volumes randomized\n\n";

    // Step 3: Rename columns
    echo "Step 3: Renaming columns...\n";
    
    DB::statement("ALTER TABLE products CHANGE COLUMN stock_quantity quantity INT NOT NULL DEFAULT 0");
    echo "✓ stock_quantity → quantity\n";
    
    DB::statement("ALTER TABLE products CHANGE COLUMN min_stock_level alert_quantity INT NOT NULL DEFAULT 10");
    echo "✓ min_stock_level → alert_quantity\n";
    
    DB::statement("ALTER TABLE products CHANGE COLUMN images photos LONGTEXT NULL");
    echo "✓ images → photos\n\n";

    // Step 4: Drop unused columns
    echo "Step 4: Dropping unused columns...\n";
    
    $columnsToDrop = ['description', 'category_id', 'brand_id', 'cost_price', 'barcode', 'reserved_qty', 'size', 'image'];
    
    foreach ($columnsToDrop as $column) {
        try {
            DB::statement("ALTER TABLE products DROP COLUMN {$column}");
            echo "✓ Dropped {$column}\n";
        } catch (\Exception $e) {
            echo "⚠ {$column} already dropped or doesn't exist\n";
        }
    }

    echo "\n=== Restructure Complete! ===\n\n";

    // Show final structure
    echo "Final Products Table Structure:\n";
    echo str_repeat("-", 80) . "\n";
    
    $columns = DB::select('SHOW COLUMNS FROM products');
    foreach ($columns as $col) {
        echo sprintf("%-20s | %-20s | Default: %s\n", 
            $col->Field, 
            $col->Type, 
            $col->Default ?? 'NULL'
        );
    }
    
    echo "\n✅ All done! Products table is ready for perfume inventory.\n";

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
