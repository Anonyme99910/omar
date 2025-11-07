<?php

echo "=== Testing Public API Routes ===\n\n";

$baseUrl = 'http://localhost/parfumes/backend/public/api';

// Test products endpoint
echo "Testing GET /products...\n";
$ch = curl_init("$baseUrl/products");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Products endpoint: OK (200)\n";
    $data = json_decode($response, true);
    echo "   Total products: " . count($data['data']) . "\n";
} else {
    echo "❌ Products endpoint: FAILED ($httpCode)\n";
}

// Test customers endpoint
echo "\nTesting GET /customers...\n";
$ch = curl_init("$baseUrl/customers");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Customers endpoint: OK (200)\n";
} else {
    echo "❌ Customers endpoint: FAILED ($httpCode)\n";
}

// Test sales endpoint
echo "\nTesting GET /sales...\n";
$ch = curl_init("$baseUrl/sales");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Sales endpoint: OK (200)\n";
} else {
    echo "❌ Sales endpoint: FAILED ($httpCode)\n";
}

echo "\n=== All public routes tested ===\n";
