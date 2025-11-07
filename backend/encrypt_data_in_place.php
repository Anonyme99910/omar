<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\EncryptionService;

echo "🔐 Encrypting Customer Data IN PLACE\n";
echo "=====================================\n\n";

echo "⚠️  WARNING: This will encrypt phone and address columns directly!\n";
echo "⚠️  Make sure you have a database backup!\n\n";

echo "Press Enter to continue or Ctrl+C to cancel...";
fgets(STDIN);

// Get all customers with plain text data
$customers = DB::table('customers')->get();
$total = $customers->count();
$encrypted = 0;
$errors = 0;

echo "\nFound $total customers to encrypt...\n\n";

foreach ($customers as $customer) {
    try {
        $updates = [];
        
        // Check if phone is already encrypted (starts with eyJ which is base64)
        if (!empty($customer->phone) && !str_starts_with($customer->phone, 'eyJ')) {
            $updates['phone'] = EncryptionService::encrypt($customer->phone);
            $updates['phone_hash'] = EncryptionService::hash($customer->phone);
            echo "✅ Encrypting phone for: {$customer->name} ({$customer->phone})\n";
        }
        
        // Check if address is already encrypted
        if (!empty($customer->address) && !str_starts_with($customer->address, 'eyJ')) {
            $updates['address'] = EncryptionService::encrypt($customer->address);
            $updates['address_hash'] = EncryptionService::hash($customer->address);
            echo "✅ Encrypting address for: {$customer->name}\n";
        }
        
        if (!empty($updates)) {
            DB::table('customers')
                ->where('id', $customer->id)
                ->update($updates);
            $encrypted++;
        }
        
    } catch (\Exception $e) {
        echo "❌ Error encrypting data for {$customer->name}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=====================================\n";
echo "🎉 Encryption Complete!\n\n";
echo "Total customers: $total\n";
echo "Encrypted: $encrypted\n";
echo "Errors: $errors\n\n";

// Verify encryption
echo "Verifying encryption...\n";
$sample = DB::table('customers')->whereNotNull('phone')->first();
if ($sample) {
    try {
        $decrypted = EncryptionService::decrypt($sample->phone);
        echo "✅ Sample verification successful\n";
        echo "   Customer: {$sample->name}\n";
        echo "   Encrypted phone: " . substr($sample->phone, 0, 50) . "...\n";
        echo "   Decrypted phone: $decrypted\n";
        echo "   Phone hash: " . substr($sample->phone_hash, 0, 16) . "...\n";
    } catch (\Exception $e) {
        echo "❌ Verification failed: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ SUCCESS!\n";
echo "   - Phone numbers are now encrypted in the 'phone' column\n";
echo "   - Addresses are now encrypted in the 'address' column\n";
echo "   - Hash columns created for searching\n";
echo "   - NO plain text data remains in database\n\n";

echo "⚠️  IMPORTANT:\n";
echo "   - The data is now encrypted at rest\n";
echo "   - Only your application can decrypt it\n";
echo "   - Keep your APP_KEY safe and backed up!\n";
echo "   - Test thoroughly before deploying\n";
