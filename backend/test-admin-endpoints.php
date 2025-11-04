<?php

echo "===========================================\n";
echo "  TESTING ADMIN ENDPOINTS\n";
echo "===========================================\n\n";

// Test 1: Login and get token
echo "[1/4] Testing admin login...\n";
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
    $token = $data['token'];
    echo "✅ Login successful\n";
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Login failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
    exit(1);
}

// Test 2: Get users
echo "[2/4] Testing /api/admin/users...\n";
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
    $users = json_decode($response, true);
    echo "✅ Users endpoint working\n";
    echo "   Total users: " . ($users['total'] ?? 0) . "\n";
    echo "   Users in page: " . count($users['data'] ?? []) . "\n\n";
} else {
    echo "❌ Users endpoint failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
}

// Test 3: Get properties
echo "[3/4] Testing /api/admin/properties...\n";
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
    $properties = json_decode($response, true);
    echo "✅ Properties endpoint working\n";
    echo "   Total properties: " . ($properties['total'] ?? 0) . "\n";
    echo "   Properties in page: " . count($properties['data'] ?? []) . "\n\n";
} else {
    echo "❌ Properties endpoint failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
}

// Test 4: Get dashboard
echo "[4/4] Testing /api/admin/dashboard...\n";
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
    $dashboard = json_decode($response, true);
    echo "✅ Dashboard endpoint working\n";
    echo "   Total users: " . ($dashboard['total_users'] ?? 0) . "\n";
    echo "   Total properties: " . ($dashboard['total_properties'] ?? 0) . "\n";
    echo "   Pending: " . ($dashboard['pending_properties'] ?? 0) . "\n";
    echo "   Approved: " . ($dashboard['approved_properties'] ?? 0) . "\n\n";
} else {
    echo "❌ Dashboard endpoint failed (HTTP $httpCode)\n";
    echo "   Response: $response\n\n";
}

echo "===========================================\n";
echo "  TEST COMPLETE!\n";
echo "===========================================\n";
