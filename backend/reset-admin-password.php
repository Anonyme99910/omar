<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "===========================================\n";
echo "  RESETTING ADMIN PASSWORD\n";
echo "===========================================\n\n";

$admin = User::where('email', 'admin@parfumes.com')->first();

if ($admin) {
    echo "Found admin user: " . $admin->email . "\n\n";
    
    // Update password
    $admin->password = Hash::make('Admin@123');
    $admin->is_admin = true;
    $admin->is_active = true;
    $admin->save();
    
    echo "✅ Password reset successfully!\n\n";
    
    // Verify the password
    if (Hash::check('Admin@123', $admin->password)) {
        echo "✅ Password verification: SUCCESS\n\n";
    } else {
        echo "❌ Password verification: FAILED\n\n";
    }
    
    echo "===========================================\n";
    echo "  ADMIN CREDENTIALS\n";
    echo "===========================================\n\n";
    echo "URL: http://localhost/parfumes/admin/\n";
    echo "Email: admin@parfumes.com\n";
    echo "Password: Admin@123\n\n";
    echo "User Details:\n";
    echo "- Name: " . $admin->full_name . "\n";
    echo "- Phone: " . $admin->phone_number . "\n";
    echo "- Is Admin: " . ($admin->is_admin ? 'Yes' : 'No') . "\n";
    echo "- Is Active: " . ($admin->is_active ? 'Yes' : 'No') . "\n\n";
    
} else {
    echo "❌ Admin user not found!\n";
    echo "Creating new admin user...\n\n";
    
    $admin = User::create([
        'email' => 'admin@parfumes.com',
        'password' => Hash::make('Admin@123'),
        'full_name' => 'Super Admin',
        'phone_number' => '12345678',
        'is_admin' => true,
        'is_active' => true
    ]);
    
    echo "✅ Admin user created!\n\n";
}

echo "===========================================\n";
echo "  READY TO LOGIN!\n";
echo "===========================================\n";
