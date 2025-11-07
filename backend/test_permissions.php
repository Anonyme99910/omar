<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Permissions Storage...\n\n";

// Find test user
$user = App\Models\User::where('name', 'test')->first();

if (!$user) {
    echo "❌ User 'test' not found\n";
    exit;
}

echo "User: {$user->name}\n";
echo "Role: {$user->role}\n";
echo "Permissions: " . print_r($user->permissions, true) . "\n";

// Try to update permissions
echo "\nUpdating permissions...\n";
$newPermissions = ['dashboard', 'pos', 'invoices'];
$user->permissions = $newPermissions;
$saved = $user->save();

echo "Save result: " . ($saved ? 'SUCCESS' : 'FAILED') . "\n";

// Reload from database
$user->refresh();
echo "\nAfter reload:\n";
echo "Permissions: " . print_r($user->permissions, true) . "\n";
