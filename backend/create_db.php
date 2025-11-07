<?php

try {
    $conn = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE DATABASE IF NOT EXISTS perfume_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    
    echo "Database 'perfume_store' created successfully or already exists.\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

$conn = null;
