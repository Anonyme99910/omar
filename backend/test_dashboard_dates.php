<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Dashboard Date Logic...\n\n";
echo "Timezone: " . config('app.timezone') . "\n";
echo "Current Server Time: " . now()->format('Y-m-d H:i:s') . "\n\n";

$today = now()->startOfDay();
$tomorrow = now()->addDay()->startOfDay();
$thisMonth = now()->startOfMonth();
$nextMonth = now()->addMonth()->startOfMonth();

echo "Date Ranges:\n";
echo "  Today: " . $today->format('Y-m-d H:i:s') . " to " . $tomorrow->format('Y-m-d H:i:s') . "\n";
echo "  This Month: " . $thisMonth->format('Y-m-d H:i:s') . " to " . $nextMonth->format('Y-m-d H:i:s') . "\n\n";

// Get today's sales
$todaySales = App\Models\Sale::where('created_at', '>=', $today)
    ->where('created_at', '<', $tomorrow)
    ->where('status', '!=', 'void')
    ->get();

echo "Today's Sales (" . $todaySales->count() . " orders):\n";
foreach ($todaySales as $sale) {
    echo "  - Invoice: {$sale->invoice_number}, Amount: {$sale->total}, Created: " . $sale->created_at->format('Y-m-d H:i:s') . "\n";
}

if ($todaySales->count() === 0) {
    echo "  ✅ No sales today (correct for new day)\n";
}

echo "\n";

// Get yesterday's sales
$yesterday = now()->subDay()->startOfDay();
$yesterdaySales = App\Models\Sale::where('created_at', '>=', $yesterday)
    ->where('created_at', '<', $today)
    ->where('status', '!=', 'void')
    ->get();

echo "Yesterday's Sales (" . $yesterdaySales->count() . " orders):\n";
foreach ($yesterdaySales as $sale) {
    echo "  - Invoice: {$sale->invoice_number}, Amount: {$sale->total}, Created: " . $sale->created_at->format('Y-m-d H:i:s') . "\n";
}

echo "\n";

// Get this month's sales
$monthSales = App\Models\Sale::where('created_at', '>=', $thisMonth)
    ->where('created_at', '<', $nextMonth)
    ->where('status', '!=', 'void')
    ->get();

echo "This Month's Sales (" . $monthSales->count() . " orders):\n";
$monthTotal = $monthSales->sum('total');
echo "  Total: EGP " . number_format($monthTotal, 2) . "\n";

echo "\n✅ Date logic test complete!\n";
