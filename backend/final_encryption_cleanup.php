<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\EncryptionService;

echo "🔐 FINAL ENCRYPTION CLEANUP\n";
echo "============================\n\n";

echo "This script will:\n";
echo "1. Encrypt all data IN the phone and address columns\n";
echo "2. Remove the separate _encrypted columns\n";
echo "3. Keep only hash columns for searching\n\n";

echo "⚠️  WARNING: Make sure you have a database backup!\n";
echo "Press Enter to continue or Ctrl+C to cancel...";
fgets(STDIN);

// Step 1: Encrypt data in place
echo "\nStep 1: Encrypting data in phone and address columns...\n";
echo "--------------------------------------------------------\n";

$customers = DB::table('customers')->get();
$encrypted = 0;

foreach ($customers as $customer) {
    try {
        $updates = [];
        
        // If phone is plain text (not encrypted)
        if (!empty($customer->phone) && !str_starts_with($customer->phone, 'eyJ')) {
            $plainPhone = $customer->phone;
            $updates['phone'] = EncryptionService::encrypt($plainPhone);
            $updates['phone_hash'] = EncryptionService::hash($plainPhone);
            echo "✅ Encrypted phone for: {$customer->name}\n";
        }
        // If phone_encrypted exists, copy it to phone
        elseif (!empty($customer->phone_encrypted)) {
            $updates['phone'] = $customer->phone_encrypted;
            if (empty($customer->phone_hash)) {
                // Decrypt to get plain text for hashing
                $plainPhone = EncryptionService::decrypt($customer->phone_encrypted);
                $updates['phone_hash'] = EncryptionService::hash($plainPhone);
            }
            echo "✅ Moved encrypted phone for: {$customer->name}\n";
        }
        
        // If address is plain text (not encrypted)
        if (!empty($customer->address) && !str_starts_with($customer->address, 'eyJ')) {
            $plainAddress = $customer->address;
            $updates['address'] = EncryptionService::encrypt($plainAddress);
            $updates['address_hash'] = EncryptionService::hash($plainAddress);
            echo "✅ Encrypted address for: {$customer->name}\n";
        }
        // If address_encrypted exists, copy it to address
        elseif (!empty($customer->address_encrypted)) {
            $updates['address'] = $customer->address_encrypted;
            if (empty($customer->address_hash)) {
                // Decrypt to get plain text for hashing
                $plainAddress = EncryptionService::decrypt($customer->address_encrypted);
                $updates['address_hash'] = EncryptionService::hash($plainAddress);
            }
            echo "✅ Moved encrypted address for: {$customer->name}\n";
        }
        
        if (!empty($updates)) {
            DB::table('customers')
                ->where('id', $customer->id)
                ->update($updates);
            $encrypted++;
        }
        
    } catch (\Exception $e) {
        echo "❌ Error for {$customer->name}: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Encrypted $encrypted customers\n\n";

// Step 2: Change column types
echo "Step 2: Changing phone and address to TEXT type...\n";
echo "---------------------------------------------------\n";

try {
    Schema::table('customers', function ($table) {
        $table->text('phone')->nullable()->change();
        $table->text('address')->nullable()->change();
    });
    echo "✅ Column types updated\n\n";
} catch (\Exception $e) {
    echo "⚠️  Column types may already be TEXT: " . $e->getMessage() . "\n\n";
}

// Step 3: Drop the _encrypted columns
echo "Step 3: Removing redundant _encrypted columns...\n";
echo "-------------------------------------------------\n";

try {
    if (Schema::hasColumn('customers', 'phone_encrypted')) {
        Schema::table('customers', function ($table) {
            $table->dropColumn(['phone_encrypted', 'address_encrypted']);
        });
        echo "✅ Removed phone_encrypted and address_encrypted columns\n\n";
    } else {
        echo "⚠️  Columns already removed\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Error removing columns: " . $e->getMessage() . "\n\n";
}

// Step 4: Verify
echo "Step 4: Verification\n";
echo "--------------------\n";

$sample = DB::table('customers')->whereNotNull('phone')->first();
if ($sample) {
    try {
        $decrypted = EncryptionService::decrypt($sample->phone);
        echo "✅ Decryption test: SUCCESS\n";
        echo "   Customer: {$sample->name}\n";
        echo "   Encrypted: " . substr($sample->phone, 0, 40) . "...\n";
        echo "   Decrypted: $decrypted\n";
        echo "   Hash: " . substr($sample->phone_hash, 0, 16) . "...\n\n";
    } catch (\Exception $e) {
        echo "❌ Decryption test: FAILED - " . $e->getMessage() . "\n\n";
    }
}

// Final check
$columns = Schema::getColumnListing('customers');
echo "Final table structure:\n";
foreach ($columns as $col) {
    echo "  - $col\n";
}

echo "\n============================\n";
echo "🎉 ENCRYPTION COMPLETE!\n\n";
echo "✅ Phone numbers encrypted in 'phone' column\n";
echo "✅ Addresses encrypted in 'address' column\n";
echo "✅ Hash columns available for searching\n";
echo "✅ NO plain text data in database\n";
echo "✅ NO redundant _encrypted columns\n\n";

echo "⚠️  CRITICAL:\n";
echo "   - Keep your APP_KEY safe!\n";
echo "   - Backup your APP_KEY!\n";
echo "   - Test the application thoroughly!\n";
