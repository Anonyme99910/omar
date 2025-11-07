<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::statement('ALTER TABLE users ADD COLUMN permissions JSON NULL AFTER role');
    echo "✅ Permissions column added successfully!\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✅ Permissions column already exists!\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}
