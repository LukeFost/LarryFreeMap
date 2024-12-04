<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
            $_ENV[trim($key)] = trim($value);
        }
    }
}

require_once __DIR__ . '/../src/Database.php';

try {
    // Debug: Print environment variables
    echo "Database Configuration:\n";
    echo "Host: " . getenv('DB_HOST') . "\n";
    echo "Port: " . getenv('DB_PORT') . "\n";
    echo "Database: " . getenv('DB_NAME') . "\n";
    echo "User: " . getenv('DB_USER') . "\n";
    
    // Test database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "\n✅ Database connection successful\n";

    // Test providers table
    $stmt = $conn->query('SELECT COUNT(*) FROM providers');
    $count = $stmt->fetchColumn();
    echo "✅ Providers table accessible. Count: $count\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
