<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "===========================================\n";
echo "  CREATING ADMIN USER\n";
echo "===========================================\n\n";

// Check if admin exists
$admin = User::where('email', 'admin@parfumes.com')->first();

if ($admin) {
    echo "✅ Admin user already exists!\n\n";
    echo "Email: " . $admin->email . "\n";
    echo "Name: " . $admin->full_name . "\n";
    echo "Is Admin: " . ($admin->is_admin ? 'Yes' : 'No') . "\n";
    echo "Is Active: " . ($admin->is_active ? 'Yes' : 'No') . "\n\n";
    
    // Update to ensure is_admin is true
    if (!$admin->is_admin) {
        $admin->is_admin = true;
        $admin->save();
        echo "✅ Updated user to admin\n";
    }
} else {
    echo "Creating new admin user...\n\n";
    
    try {
        $admin = User::create([
            'email' => 'admin@parfumes.com',
            'password' => Hash::make('Admin@123'),
            'full_name' => 'Super Admin',
            'phone_number' => '12345678',
            'is_admin' => true,
            'is_active' => true
        ]);
        
        echo "✅ Admin user created successfully!\n\n";
        echo "Email: admin@parfumes.com\n";
        echo "Password: Admin@123\n";
        echo "Name: Super Admin\n\n";
    } catch (\Exception $e) {
        echo "❌ Error creating admin: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "===========================================\n";
echo "  ADMIN CREDENTIALS\n";
echo "===========================================\n\n";
echo "URL: http://localhost/parfumes/admin/\n";
echo "Email: admin@parfumes.com\n";
echo "Password: Admin@123\n\n";
echo "===========================================\n";
echo "  READY TO LOGIN!\n";
echo "===========================================\n";
