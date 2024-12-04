<?php
require_once __DIR__ . '/../../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../src/Database.php';

AuthMiddleware::requireRole('admin');

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all providers with building count
        $sql = "SELECT p.*, COUNT(b.id) as building_count 
                FROM providers p 
                LEFT JOIN buildings b ON p.id = b.provider_id 
                GROUP BY p.id 
                ORDER BY p.created_at DESC";
        
        $providers = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $providers]);
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        
        if (!$id) {
            throw new Exception('Provider ID is required');
        }
        
        // Start transaction
        $db->beginTransaction();
        
        try {
            // Delete associated units first
            $db->prepare("DELETE FROM units WHERE building_id IN (SELECT id FROM buildings WHERE provider_id = ?)")
               ->execute([$id]);
            
            // Delete buildings
            $db->prepare("DELETE FROM buildings WHERE provider_id = ?")
               ->execute([$id]);
            
            // Delete provider
            $db->prepare("DELETE FROM providers WHERE id = ?")
               ->execute([$id]);
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Provider deleted successfully']);
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
