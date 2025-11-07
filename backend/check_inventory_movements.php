<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking inventory_movements Table ===\n\n";

$columns = DB::select("DESCRIBE inventory_movements");

echo "Columns:\n";
foreach ($columns as $column) {
    echo "- {$column->Field}: {$column->Type}\n";
    if ($column->Field === 'type') {
        echo "  ⚠️ Type column details: {$column->Type}\n";
    }
}

// Check if type is ENUM
$result = DB::select("SHOW COLUMNS FROM inventory_movements WHERE Field = 'type'");
if (!empty($result)) {
    echo "\n=== Type Column Details ===\n";
    echo "Type: {$result[0]->Type}\n";
    echo "Null: {$result[0]->Null}\n";
    echo "Default: {$result[0]->Default}\n";
}
