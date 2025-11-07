<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sale;

echo "=== Sales Data Check ===\n\n";

$totalSales = Sale::count();
echo "Total sales in database: {$totalSales}\n";

$salesToday = Sale::whereDate('created_at', today())->count();
echo "Sales today: {$salesToday}\n";

$salesThisMonth = Sale::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();
echo "Sales this month: {$salesThisMonth}\n";

$salesThisYear = Sale::whereYear('created_at', now()->year)->count();
echo "Sales this year: {$salesThisYear}\n\n";

echo "=== Recent Sales ===\n";
$recentSales = Sale::with('customer')->latest()->limit(5)->get();

foreach ($recentSales as $sale) {
    echo "ID: {$sale->id} | Date: {$sale->created_at} | Total: {$sale->total} | Customer: " . ($sale->customer->name ?? 'Walk-in') . "\n";
}

echo "\n=== Date Range ===\n";
$oldest = Sale::oldest()->first();
$newest = Sale::latest()->first();

if ($oldest) {
    echo "Oldest sale: {$oldest->created_at}\n";
}
if ($newest) {
    echo "Newest sale: {$newest->created_at}\n";
}
