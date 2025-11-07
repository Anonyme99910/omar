<?php
$host = 'localhost';
$dbname = 'perfume_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("DROP TABLE IF EXISTS package_items");
    echo "✅ Dropped old package_items table\n";
    
    $sql = "CREATE TABLE package_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        package_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_package (package_id),
        INDEX idx_product (product_id),
        UNIQUE KEY unique_package_product (package_id, product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✅ Package items table created successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
