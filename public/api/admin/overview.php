<?php
require_once __DIR__ . '/../../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../src/Database.php';

AuthMiddleware::requireRole('admin');

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get counts
    $providerCount = $db->query('SELECT COUNT(*) FROM providers')->fetchColumn();
    $buildingCount = $db->query('SELECT COUNT(*) FROM buildings')->fetchColumn();
    $unitCount = $db->query('SELECT COUNT(*) FROM units')->fetchColumn();
    $availableUnits = $db->query('SELECT COUNT(*) FROM units WHERE is_available = true')->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'providers' => $providerCount,
        'buildings' => $buildingCount,
        'units' => $unitCount,
        'available_units' => $availableUnits
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching overview data: ' . $e->getMessage()
    ]);
}
