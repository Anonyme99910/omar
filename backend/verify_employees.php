<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Employees Check ===\n\n";

$employees = App\Models\User::where('role', '!=', 'admin')->get();

echo "Total Employees: " . $employees->count() . "\n\n";

foreach ($employees as $emp) {
    echo sprintf(
        "- %s (%s) | Role: %s | Active: %s\n",
        $emp->name,
        $emp->email,
        $emp->role,
        $emp->is_active ? 'Yes' : 'No'
    );
}

echo "\n=== Roles Available ===\n";
echo "1. Admin (مدير)\n";
echo "2. Cashier (كاشير)\n";
echo "3. Manager (مدير فرع)\n";
echo "4. Inventory (مخزن)\n";
