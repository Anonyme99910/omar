<?php

// Script to update .env file for database sessions

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Update or add SESSION_DRIVER
if (strpos($envContent, 'SESSION_DRIVER=') !== false) {
    $envContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=database', $envContent);
    echo "✅ Updated SESSION_DRIVER to database\n";
} else {
    $envContent .= "\nSESSION_DRIVER=database\n";
    echo "✅ Added SESSION_DRIVER=database\n";
}

// Update or add SESSION_LIFETIME
if (strpos($envContent, 'SESSION_LIFETIME=') !== false) {
    $envContent = preg_replace('/SESSION_LIFETIME=.*/', 'SESSION_LIFETIME=120', $envContent);
    echo "✅ Updated SESSION_LIFETIME to 120\n";
} else {
    $envContent .= "SESSION_LIFETIME=120\n";
    echo "✅ Added SESSION_LIFETIME=120\n";
}

// Update or add SESSION_SECURE_COOKIE
if (strpos($envContent, 'SESSION_SECURE_COOKIE=') !== false) {
    $envContent = preg_replace('/SESSION_SECURE_COOKIE=.*/', 'SESSION_SECURE_COOKIE=false', $envContent);
    echo "✅ Updated SESSION_SECURE_COOKIE to false (for local development)\n";
} else {
    $envContent .= "SESSION_SECURE_COOKIE=false\n";
    echo "✅ Added SESSION_SECURE_COOKIE=false\n";
}

// Write back to .env
file_put_contents($envFile, $envContent);

echo "\n✅ .env file updated successfully!\n";
echo "\nPlease run: php artisan config:clear\n";
echo "Then restart your server.\n";
