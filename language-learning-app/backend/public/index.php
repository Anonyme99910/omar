<?php

// Simple router for API requests
$requestUri = $_SERVER['REQUEST_URI'];

// Check if this is an API request
if (strpos($requestUri, '/api') !== false) {
    // Route to API handler
    require __DIR__ . '/api/index.php';
    exit;
}

// For non-API requests, show a simple message
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'LinguaLearn API',
    'version' => '1.0',
    'endpoints' => [
        'courses' => '/api/courses',
        'login' => '/api/login',
        'register' => '/api/register',
        'guest-login' => '/api/guest-login'
    ]
]);
