<?php
header('Content-Type: application/json');

$host = '127.0.0.1';
$db = 'duolingo';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Database Check</h1>";
    
    // Check courses
    echo "<h2>Courses:</h2>";
    $stmt = $pdo->query("SELECT * FROM courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . json_encode($courses, JSON_PRETTY_PRINT) . "</pre>";
    
    // Check lessons
    echo "<h2>Lessons:</h2>";
    $stmt = $pdo->query("SELECT * FROM lessons LIMIT 5");
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . json_encode($lessons, JSON_PRETTY_PRINT) . "</pre>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
