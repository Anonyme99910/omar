<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

echo "=== Testing Date Filtering ===\n\n";

$now = new Date();
$today = date('Y-m-d');
$weekAgo = date('Y-m-d', strtotime('-7 days'));
$monthStart = date('Y-m-01');
$yearStart = date('Y-01-01');

echo "Today: $today\n";
echo "Week ago: $weekAgo\n";
echo "Month start: $monthStart\n";
echo "Year start: $yearStart\n\n";

// Test today
echo "=== Sales TODAY ($today) ===\n";
$todaySales = Sale::whereRaw('DATE(COALESCE(issue_date, created_at)) BETWEEN ? AND ?', [$today, $today])->count();
echo "Count: $todaySales\n\n";

// Test this week
echo "=== Sales THIS WEEK ($weekAgo to $today) ===\n";
$weekSales = Sale::whereRaw('DATE(COALESCE(issue_date, created_at)) BETWEEN ? AND ?', [$weekAgo, $today])->count();
echo "Count: $weekSales\n\n";

// Test this month
echo "=== Sales THIS MONTH ($monthStart to $today) ===\n";
$monthSales = Sale::whereRaw('DATE(COALESCE(issue_date, created_at)) BETWEEN ? AND ?', [$monthStart, $today])->count();
echo "Count: $monthSales\n\n";

// Test this year
echo "=== Sales THIS YEAR ($yearStart to $today) ===\n";
$yearSales = Sale::whereRaw('DATE(COALESCE(issue_date, created_at)) BETWEEN ? AND ?', [$yearStart, $today])->count();
echo "Count: $yearSales\n\n";

// Show some sample dates
echo "=== Sample Sales Dates ===\n";
$samples = Sale::latest()->limit(5)->get(['id', 'created_at', 'issue_date']);
foreach ($samples as $sale) {
    $date = $sale->issue_date ?? $sale->created_at;
    echo "ID {$sale->id}: {$date}\n";
}
