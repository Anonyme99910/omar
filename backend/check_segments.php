<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

echo "=== Current Segment Values in Database ===\n\n";

$customers = Customer::select('id', 'name', 'segment')->limit(10)->get();

foreach ($customers as $customer) {
    echo "ID: {$customer->id} | Name: {$customer->name} | Segment: '{$customer->segment}'\n";
}

echo "\n=== Segment Distribution ===\n";
$segments = DB::table('customers')
    ->select('segment', DB::raw('count(*) as count'))
    ->groupBy('segment')
    ->get();

foreach ($segments as $seg) {
    echo "{$seg->segment}: {$seg->count} customers\n";
}

echo "\n=== Column Info ===\n";
$columns = DB::select("SHOW COLUMNS FROM customers WHERE Field = 'segment'");
foreach ($columns as $col) {
    echo "Type: {$col->Type}\n";
    echo "Null: {$col->Null}\n";
    echo "Default: {$col->Default}\n";
}
