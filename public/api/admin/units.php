<?php
require_once __DIR__ . '/../../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../src/Database.php';

AuthMiddleware::requireRole('admin');

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all units with building and provider info
        $sql = "SELECT u.*, b.name as building_name, p.name as provider_name 
                FROM units u 
                LEFT JOIN buildings b ON u.building_id = b.id 
                LEFT JOIN providers p ON b.provider_id = p.id 
                ORDER BY u.created_at DESC";
        
        $units = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON features
        foreach ($units as &$unit) {
            if ($unit['features']) {
                $unit['features'] = json_decode($unit['features'], true);
            }
        }
        
        echo json_encode(['success' => true, 'data' => $units]);
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        
        if (!$id) {
            throw new Exception('Unit ID is required');
        }
        
        $stmt = $db->prepare("DELETE FROM units WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Unit deleted successfully']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
