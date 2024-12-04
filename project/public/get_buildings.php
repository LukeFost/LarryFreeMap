<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Models/Building.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $building = new Building($db);
    $buildings = $building->getAll();
    error_log("get_buildings.php - Retrieved buildings: " . json_encode($buildings));
    echo json_encode($buildings);
} catch (Exception $e) {
    error_log("get_buildings.php - Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
