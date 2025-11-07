<?php
/**
 * Test customer creation
 */

$host = 'localhost';
$dbname = 'perfume_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Testing Customer Creation ===\n\n";
    
    // Test data
    $testCustomer = [
        'name' => 'عميل تجريبي',
        'phone' => '01234567890',
        'segment' => 'قطاعي'
    ];
    
    // Insert test customer
    $sql = "INSERT INTO customers (name, phone, segment, created_at, updated_at) 
            VALUES (:name, :phone, :segment, NOW(), NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $testCustomer['name'],
        'phone' => $testCustomer['phone'],
        'segment' => $testCustomer['segment']
    ]);
    
    $customerId = $pdo->lastInsertId();
    
    echo "✅ Customer created successfully!\n";
    echo "ID: $customerId\n";
    echo "Name: {$testCustomer['name']}\n";
    echo "Phone: {$testCustomer['phone']}\n";
    echo "Segment: {$testCustomer['segment']}\n\n";
    
    // Fetch the customer
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "=== Retrieved Customer ===\n";
    print_r($customer);
    
    // Clean up
    $pdo->exec("DELETE FROM customers WHERE id = $customerId");
    echo "\n✅ Test customer deleted\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
