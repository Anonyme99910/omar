<?php

echo "===========================================\n";
echo "  TESTING REGISTRATION ENDPOINT\n";
echo "===========================================\n\n";

$url = "http://localhost/parfumes/backend/public/api/register";

$data = [
    'email' => 'test' . time() . '@example.com',
    'password' => 'password123',
    'full_name' => 'Test User',
    'phone_number' => '12345678'
];

echo "Testing: POST /api/register\n";
echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

if ($httpCode === 200 || $httpCode === 201) {
    echo "✅ Registration endpoint working!\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['token'])) {
        echo "✅ Token received: " . substr($responseData['token'], 0, 20) . "...\n";
    }
    if (isset($responseData['user'])) {
        echo "✅ User created: " . $responseData['user']['email'] . "\n";
    }
} else {
    echo "❌ Registration failed!\n";
    echo "Error: $response\n";
}

echo "\n===========================================\n";
