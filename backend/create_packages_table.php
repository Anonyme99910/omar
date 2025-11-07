<?php
/**
 * Create packages table
 * Run this file once: php create_packages_table.php
 */

$host = 'localhost';
$dbname = 'perfume_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE TABLE IF NOT EXISTS packages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name_ar VARCHAR(255) NOT NULL,
        name_en VARCHAR(255) NULL,
        sku VARCHAR(100) UNIQUE NULL,
        description TEXT NULL,
        photos JSON NULL,
        
        -- Pricing for different segments
        price_قطاعي DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        price_جملة DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        price_صفحة DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        
        -- Inventory
        quantity INT NOT NULL DEFAULT 0,
        alert_quantity INT NOT NULL DEFAULT 10,
        
        -- Metadata
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        INDEX idx_sku (sku),
        INDEX idx_name_ar (name_ar),
        INDEX idx_active (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    echo "✅ Packages table created successfully!\n";
    
    // Create package_items table (which products are in each package)
    $sql2 = "CREATE TABLE IF NOT EXISTS package_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        package_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_package_product (package_id, product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql2);
    
    echo "✅ Package items table created successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
