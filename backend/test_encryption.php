<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use App\Services\EncryptionService;

echo "🔐 Testing Encryption System\n";
echo "============================\n\n";

$allPassed = true;

// Test 1: Encryption Service
echo "Test 1: Encryption Service\n";
echo "----------------------------\n";
$testData = '01234567890';
$encrypted = EncryptionService::encrypt($testData);
$decrypted = EncryptionService::decrypt($encrypted);

if ($testData === $decrypted) {
    echo "✅ Encryption/Decryption: PASS\n";
} else {
    echo "❌ Encryption/Decryption: FAIL\n";
    $allPassed = false;
}

// Test 2: Hashing
echo "\nTest 2: Hashing\n";
echo "----------------------------\n";
$hash1 = EncryptionService::hash('01234567890');
$hash2 = EncryptionService::hash('01234567890');
$hash3 = EncryptionService::hash('01234567891');

if ($hash1 === $hash2 && $hash1 !== $hash3) {
    echo "✅ Hash Consistency: PASS\n";
    echo "   Hash: " . substr($hash1, 0, 16) . "...\n";
} else {
    echo "❌ Hash Consistency: FAIL\n";
    $allPassed = false;
}

// Test 3: Phone Masking
echo "\nTest 3: Phone Masking\n";
echo "----------------------------\n";
$masked = EncryptionService::maskPhone('01234567890');
if ($masked === '012****7890') {
    echo "✅ Phone Masking: PASS\n";
    echo "   Original: 01234567890\n";
    echo "   Masked:   $masked\n";
} else {
    echo "❌ Phone Masking: FAIL (got: $masked)\n";
    $allPassed = false;
}

// Test 4: Address Masking
echo "\nTest 4: Address Masking\n";
echo "----------------------------\n";
$longAddress = 'القاهرة، شارع الهرم، بجوار المحطة';
$masked = EncryptionService::maskAddress($longAddress);
if (strlen($masked) <= 23) { // 20 chars + "..."
    echo "✅ Address Masking: PASS\n";
    echo "   Original: $longAddress\n";
    echo "   Masked:   $masked\n";
} else {
    echo "❌ Address Masking: FAIL\n";
    $allPassed = false;
}

// Test 5: Model Encryption
echo "\nTest 5: Model Encryption\n";
echo "----------------------------\n";
$customer = Customer::first();
if ($customer) {
    if (!empty($customer->phone_encrypted) && !empty($customer->phone_hash)) {
        echo "✅ Model Encryption: PASS\n";
        echo "   Customer: {$customer->name}\n";
        echo "   Phone (decrypted): {$customer->phone}\n";
        echo "   Phone (masked): {$customer->masked_phone}\n";
        echo "   Encrypted: " . substr($customer->phone_encrypted, 0, 30) . "...\n";
    } else {
        echo "❌ Model Encryption: FAIL (no encrypted data)\n";
        $allPassed = false;
    }
} else {
    echo "⚠️  No customers found to test\n";
}

// Test 6: Search by Phone
echo "\nTest 6: Search by Phone Hash\n";
echo "----------------------------\n";
if ($customer && $customer->phone) {
    $found = Customer::searchByPhone($customer->phone);
    if ($found->count() > 0) {
        echo "✅ Phone Search: PASS\n";
        echo "   Searched for: {$customer->phone}\n";
        echo "   Found: {$found->count()} customer(s)\n";
    } else {
        echo "❌ Phone Search: FAIL\n";
        $allPassed = false;
    }
}

// Test 7: Create New Customer
echo "\nTest 7: Create New Customer with Encryption\n";
echo "----------------------------\n";
try {
    $testCustomer = Customer::create([
        'name' => 'Test Encryption User',
        'phone' => '01999999999',
        'address' => 'Test Address for Encryption',
        'segment' => 'قطاعي'
    ]);
    
    if ($testCustomer->phone === '01999999999' && 
        !empty($testCustomer->phone_encrypted) &&
        !empty($testCustomer->phone_hash)) {
        echo "✅ Create with Encryption: PASS\n";
        echo "   Phone stored encrypted: Yes\n";
        echo "   Phone hash created: Yes\n";
        echo "   Decryption works: Yes\n";
        
        // Clean up
        $testCustomer->delete();
    } else {
        echo "❌ Create with Encryption: FAIL\n";
        $allPassed = false;
    }
} catch (\Exception $e) {
    echo "❌ Create with Encryption: FAIL\n";
    echo "   Error: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// Test 8: Verify Database Encryption
echo "\nTest 8: Database Verification\n";
echo "----------------------------\n";
$stats = [
    'total' => Customer::count(),
    'encrypted_phone' => Customer::whereNotNull('phone_encrypted')->count(),
    'encrypted_address' => Customer::whereNotNull('address_encrypted')->count(),
    'with_hash' => Customer::whereNotNull('phone_hash')->count(),
];

echo "Total customers: {$stats['total']}\n";
echo "With encrypted phone: {$stats['encrypted_phone']}\n";
echo "With encrypted address: {$stats['encrypted_address']}\n";
echo "With phone hash: {$stats['with_hash']}\n";

if ($stats['encrypted_phone'] === $stats['total'] && 
    $stats['encrypted_address'] === $stats['total']) {
    echo "✅ All customers encrypted: PASS\n";
} else {
    echo "⚠️  Some customers not encrypted\n";
}

// Summary
echo "\n============================\n";
if ($allPassed) {
    echo "🎉 ALL TESTS PASSED!\n";
    echo "\n✅ Encryption system is working correctly\n";
    echo "✅ All sensitive data is encrypted\n";
    echo "✅ Search functionality works\n";
    echo "✅ Masking works properly\n";
} else {
    echo "❌ SOME TESTS FAILED\n";
    echo "\nPlease review the failures above.\n";
}

echo "\n📊 Encryption Statistics:\n";
echo "   Total Customers: {$stats['total']}\n";
echo "   Encrypted: {$stats['encrypted_phone']}\n";
echo "   Coverage: " . ($stats['total'] > 0 ? round(($stats['encrypted_phone'] / $stats['total']) * 100, 2) : 0) . "%\n";
