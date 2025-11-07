<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use App\Services\EncryptionService;
use Illuminate\Support\Facades\DB;

echo "🔐 Encrypting Existing Customer Data\n";
echo "=====================================\n\n";

// Get all customers
$customers = Customer::all();
$total = $customers->count();
$encrypted = 0;
$skipped = 0;

echo "Found $total customers to process...\n\n";

foreach ($customers as $customer) {
    try {
        $updated = false;
        
        // Encrypt phone if exists and not already encrypted
        if (!empty($customer->phone) && empty($customer->phone_encrypted)) {
            $customer->phone_encrypted = EncryptionService::encrypt($customer->phone);
            $customer->phone_hash = EncryptionService::hash($customer->phone);
            $updated = true;
            echo "✅ Encrypted phone for: {$customer->name}\n";
        }
        
        // Encrypt address if exists and not already encrypted
        if (!empty($customer->address) && empty($customer->address_encrypted)) {
            $customer->address_encrypted = EncryptionService::encrypt($customer->address);
            $customer->address_hash = EncryptionService::hash($customer->address);
            $updated = true;
            echo "✅ Encrypted address for: {$customer->name}\n";
        }
        
        if ($updated) {
            $customer->save();
            $encrypted++;
        } else {
            $skipped++;
        }
        
    } catch (\Exception $e) {
        echo "❌ Error encrypting data for {$customer->name}: " . $e->getMessage() . "\n";
    }
}

echo "\n=====================================\n";
echo "🎉 Encryption Complete!\n\n";
echo "Total customers: $total\n";
echo "Encrypted: $encrypted\n";
echo "Skipped (already encrypted): $skipped\n\n";

// Verify encryption
echo "Verifying encryption...\n";
$sample = Customer::whereNotNull('phone_encrypted')->first();
if ($sample) {
    $decrypted = EncryptionService::decrypt($sample->phone_encrypted);
    echo "Sample verification:\n";
    echo "  Original: {$sample->phone}\n";
    echo "  Encrypted: " . substr($sample->phone_encrypted, 0, 50) . "...\n";
    echo "  Decrypted: $decrypted\n";
    echo "  Match: " . ($sample->phone === $decrypted ? '✅ Yes' : '❌ No') . "\n";
}

echo "\n⚠️  IMPORTANT NEXT STEPS:\n";
echo "1. Update Customer model to use encrypted fields\n";
echo "2. Update all controllers to use encryption\n";
echo "3. Test thoroughly before deploying\n";
echo "4. Consider removing plain text columns after verification\n";
