<?php
require_once __DIR__ . '/../src/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Test query
    $stmt = $conn->query('SELECT count(*) FROM providers');
    $count = $stmt->fetchColumn();
    
    echo "Connected successfully. Provider count: " . $count;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}