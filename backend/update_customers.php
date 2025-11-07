<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Updating customers to remove emails...\n";

$customers = App\Models\Customer::all();
foreach ($customers as $customer) {
    $customer->email = null;
    $customer->save();
}

echo "Done! Updated " . $customers->count() . " customers.\n";
