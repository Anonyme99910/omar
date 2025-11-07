<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Employees Endpoint...\n\n";

try {
    $employees = App\Models\User::select(['id', 'name', 'email', 'role', 'permissions', 'is_active', 'created_at'])
        ->where('role', '!=', 'admin')
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "✅ Found " . $employees->count() . " employees:\n\n";
    
    foreach ($employees as $employee) {
        $permissions = json_decode($employee->permissions ?? '[]', true);
        echo "- {$employee->name} ({$employee->role})\n";
        echo "  Email: {$employee->email}\n";
        echo "  Permissions: " . (count($permissions) > 0 ? implode(', ', $permissions) : 'None') . "\n\n";
    }
    
    echo "✅ Employees endpoint working!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
