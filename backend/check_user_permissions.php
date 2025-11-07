<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking User Permissions...\n\n";

$users = App\Models\User::where('role', '!=', 'admin')->get();

foreach ($users as $user) {
    echo "User: {$user->name}\n";
    echo "Role: {$user->role}\n";
    echo "Permissions: " . print_r($user->permissions, true) . "\n";
    echo "---\n\n";
}
