<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;

echo "=== Testing Customer Segment Update ===\n\n";

// Get a customer
$customer = Customer::first();
echo "Before Update:\n";
echo "ID: {$customer->id}\n";
echo "Name: {$customer->name}\n";
echo "Phone: {$customer->phone}\n";
echo "Segment: {$customer->segment}\n\n";

// Update segment
$newSegment = $customer->segment === 'جملة' ? 'قطاعي' : 'جملة';
$customer->segment = $newSegment;
$customer->save();

echo "After Update:\n";
$customer->refresh();
echo "ID: {$customer->id}\n";
echo "Name: {$customer->name}\n";
echo "Phone: {$customer->phone}\n";
echo "Segment: {$customer->segment}\n\n";

echo "✅ Update successful!\n";
