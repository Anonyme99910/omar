<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "  CHECKING personal_access_tokens TABLE\n";
echo "===========================================\n\n";

$columns = DB::select("DESCRIBE personal_access_tokens");

echo "Table Structure:\n";
echo str_repeat("-", 80) . "\n";
printf("%-25s %-20s %-10s %-10s\n", "Field", "Type", "Null", "Key");
echo str_repeat("-", 80) . "\n";

foreach ($columns as $column) {
    printf("%-25s %-20s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo str_repeat("-", 80) . "\n\n";

// Check tokenable_id type
$tokenableIdColumn = collect($columns)->firstWhere('Field', 'tokenable_id');
if ($tokenableIdColumn) {
    echo "tokenable_id Type: " . $tokenableIdColumn->Type . "\n";
    if (str_contains($tokenableIdColumn->Type, 'char')) {
        echo "✅ Correct! Using CHAR (UUID compatible)\n";
    } else {
        echo "❌ Wrong! Should be CHAR for UUID\n";
    }
}

echo "\n===========================================\n";
echo "  TABLE CHECK COMPLETE\n";
echo "===========================================\n";
