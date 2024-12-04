<?php
require_once __DIR__ . '/../../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../src/Database.php';

AuthMiddleware::requireRole('admin');

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all buildings with provider info and unit count
        $sql = "SELECT b.*, p.name as provider_name, COUNT(u.id) as unit_count 
                FROM buildings b 
                LEFT JOIN providers p ON b.provider_id = p.id 
                LEFT JOIN units u ON b.id = u.building_id 
                GROUP BY b.id, p.name 
                ORDER BY b.created_at DESC";
        
        $buildings = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON details
        foreach ($buildings as &$building) {
            if ($building['details']) {
                $building['details'] = json_decode($building['details'], true);
            }
        }
        
        echo json_encode(['success' => true, 'data' => $buildings]);
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        
        if (!$id) {
            throw new Exception('Building ID is required');
        }
        
        // Start transaction
        $db->beginTransaction();
        
        try {
            // Delete associated units first
            $db->prepare("DELETE FROM units WHERE building_id = ?")
               ->execute([$id]);
            
            // Delete building
            $db->prepare("DELETE FROM buildings WHERE id = ?")
               ->execute([$id]);
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Building deleted successfully']);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
