<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Clearing customers...\n";
App\Models\Customer::truncate();

echo "Seeding customers...\n";
Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\CustomerSeeder', '--force' => true]);

echo "Done! Customers reseeded.\n";
