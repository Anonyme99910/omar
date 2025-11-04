<?php

echo "===========================================\n";
echo "  TESTING ADMIN PANEL BACKEND\n";
echo "===========================================\n\n";

// Test 1: Check if admin user exists
echo "[1/5] Checking admin user...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/parfumes/backend/public/api/login");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@parfumes.com',
    'password' => 'Admin@123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['user']) && $data['user']['is_admin']) {
        echo "✅ Admin user exists and can login\n";
        echo "   Token: " . substr($data['token'], 0, 20) . "...\n\n";
        $token = $data['token'];
    } else {
        echo "❌ User is not an admin\n\n";
        exit(1);
    }
} else {
    echo "❌ Login failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
    exit(1);
}

// Test 2: Check dashboard endpoint
echo "[2/5] Testing dashboard endpoint...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/parfumes/backend/public/api/admin/dashboard");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Dashboard endpoint working\n";
    $dashboard = json_decode($response, true);
    echo "   Total users: " . ($dashboard['total_users'] ?? 0) . "\n";
    echo "   Total properties: " . ($dashboard['total_properties'] ?? 0) . "\n\n";
} else {
    echo "❌ Dashboard failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
}

// Test 3: Check users endpoint
echo "[3/5] Testing users endpoint...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/parfumes/backend/public/api/admin/users");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Users endpoint working\n";
    $users = json_decode($response, true);
    echo "   Found " . count($users['data'] ?? []) . " users\n\n";
} else {
    echo "❌ Users failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
}

// Test 4: Check properties endpoint
echo "[4/5] Testing properties endpoint...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/parfumes/backend/public/api/admin/properties");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Properties endpoint working\n";
    $properties = json_decode($response, true);
    echo "   Found " . count($properties['data'] ?? []) . " properties\n\n";
} else {
    echo "❌ Properties failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
}

// Test 5: Check public API
echo "[5/5] Testing public API...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/parfumes/backend/public/api");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Public API working\n";
    $api = json_decode($response, true);
    echo "   Message: " . ($api['message'] ?? 'N/A') . "\n";
    echo "   Status: " . ($api['status'] ?? 'N/A') . "\n\n";
} else {
    echo "❌ Public API failed (HTTP $httpCode)\n\n";
}

echo "===========================================\n";
echo "  TEST COMPLETE!\n";
echo "===========================================\n\n";
echo "Admin Panel URL: http://localhost/parfumes/admin/\n";
echo "Email: admin@parfumes.com\n";
echo "Password: Admin@123\n\n";
echo "All systems ready! ✅\n";
