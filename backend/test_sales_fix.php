<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Sales Scopes...\n\n";

try {
    // Test unpaid scope
    $unpaid = App\Models\Sale::unpaid()->count();
    echo "✅ Unpaid sales: $unpaid\n";
    
    // Test partially paid scope
    $partial = App\Models\Sale::partiallyPaid()->count();
    echo "✅ Partially paid sales: $partial\n";
    
    // Test paid scope
    $paid = App\Models\Sale::paid()->count();
    echo "✅ Paid sales: $paid\n";
    
    // Test void scope
    $void = App\Models\Sale::void()->count();
    echo "✅ Void sales: $void\n";
    
    // Test total
    $total = App\Models\Sale::count();
    echo "✅ Total sales: $total\n";
    
    echo "\n✅ All scopes working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
