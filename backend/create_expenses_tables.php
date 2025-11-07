<?php
/**
 * Create Expenses Management Tables
 */

$host = 'localhost';
$dbname = 'perfume_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Creating Expenses Tables ===\n\n";
    
    // 1. Expense Types Table
    $pdo->exec("DROP TABLE IF EXISTS expenses");
    $pdo->exec("DROP TABLE IF EXISTS expense_types");
    
    $pdo->exec("
        CREATE TABLE expense_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name_ar VARCHAR(255) NOT NULL,
            name_en VARCHAR(255),
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ expense_types table created\n";
    
    // 2. Expenses Table
    $pdo->exec("
        CREATE TABLE expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            expense_type_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            expense_date DATE NOT NULL,
            recurrence_type ENUM('once', 'monthly', 'yearly') DEFAULT 'once',
            remarks TEXT,
            is_fixed BOOLEAN DEFAULT FALSE,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (expense_type_id) REFERENCES expense_types(id) ON DELETE CASCADE,
            INDEX idx_expense_date (expense_date),
            INDEX idx_recurrence (recurrence_type),
            INDEX idx_expense_type (expense_type_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ expenses table created\n";
    
    // Insert sample expense types
    $sampleTypes = [
        ['الإيجار', 'Rent', 'إيجار المحل الشهري'],
        ['الكهرباء', 'Electricity', 'فاتورة الكهرباء'],
        ['المياه', 'Water', 'فاتورة المياه'],
        ['الرواتب', 'Salaries', 'رواتب الموظفين'],
        ['الصيانة', 'Maintenance', 'صيانة المعدات'],
        ['التسويق', 'Marketing', 'مصاريف التسويق والإعلان'],
        ['المواصلات', 'Transportation', 'مصاريف النقل والتوصيل'],
        ['اللوازم المكتبية', 'Office Supplies', 'أدوات مكتبية ولوازم'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO expense_types (name_ar, name_en, description) VALUES (?, ?, ?)");
    foreach ($sampleTypes as $type) {
        $stmt->execute($type);
    }
    echo "✅ Sample expense types inserted\n";
    
    // Insert sample expenses
    $sampleExpenses = [
        [1, 5000.00, '2025-01-01', 'monthly', 'إيجار شهر يناير', true],
        [2, 350.00, '2025-01-15', 'monthly', 'فاتورة كهرباء يناير', false],
        [3, 150.00, '2025-01-15', 'monthly', 'فاتورة مياه يناير', false],
        [4, 8000.00, '2025-01-01', 'monthly', 'رواتب الموظفين - يناير', true],
        [5, 500.00, '2025-01-20', 'once', 'صيانة التكييف', false],
        [6, 1200.00, '2025-01-10', 'monthly', 'حملة إعلانية على فيسبوك', false],
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO expenses (expense_type_id, amount, expense_date, recurrence_type, remarks, is_fixed) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    foreach ($sampleExpenses as $expense) {
        $stmt->execute($expense);
    }
    echo "✅ Sample expenses inserted\n";
    
    echo "\n=== Tables Created Successfully! ===\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
