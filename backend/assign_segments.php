<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;

echo "=== Assigning Random Segments to Customers ===\n\n";

$segments = ['جملة', 'قطاعي', 'صفحة'];
$customers = Customer::all();

echo "Total customers: " . $customers->count() . "\n\n";

foreach ($customers as $customer) {
    $randomSegment = $segments[array_rand($segments)];
    $customer->segment = $randomSegment;
    $customer->save();
    
    echo "Customer #{$customer->id} ({$customer->name}): {$randomSegment}\n";
}

echo "\n=== Segment Distribution ===\n";
foreach ($segments as $segment) {
    $count = Customer::where('segment', $segment)->count();
    echo "{$segment}: {$count} customers\n";
}

echo "\nDone!\n";
