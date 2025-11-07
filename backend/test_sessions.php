<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Session Configuration...\n\n";

// Check configuration
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . " minutes\n";
echo "Session Encrypt: " . (config('session.encrypt') ? 'Yes' : 'No') . "\n";
echo "Session Secure: " . (config('session.secure') ? 'Yes' : 'No') . "\n";
echo "Session HttpOnly: " . (config('session.http_only') ? 'Yes' : 'No') . "\n";
echo "Session SameSite: " . config('session.same_site') . "\n\n";

// Check if sessions table exists
try {
    $tableExists = DB::select("SHOW TABLES LIKE 'sessions'");
    if (count($tableExists) > 0) {
        echo "✅ Sessions table exists\n";
        
        // Count sessions
        $count = DB::table('sessions')->count();
        echo "Current sessions in database: $count\n\n";
        
        // Show recent sessions
        if ($count > 0) {
            echo "Recent sessions:\n";
            $sessions = DB::table('sessions')
                ->orderBy('last_activity', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($sessions as $session) {
                $lastActivity = date('Y-m-d H:i:s', $session->last_activity);
                echo "- ID: " . substr($session->id, 0, 10) . "... | Last Activity: $lastActivity\n";
            }
        }
    } else {
        echo "❌ Sessions table does not exist!\n";
        echo "Please run: php artisan migrate\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
