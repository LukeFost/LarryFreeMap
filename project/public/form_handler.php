<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Debug log environment variables
error_log("Environment variables after loading:");
error_log("SUPABASE_URL: " . getenv('SUPABASE_URL'));
error_log("SUPABASE_ANON_KEY: " . getenv('SUPABASE_ANON_KEY'));

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Models/Provider.php';
require_once __DIR__ . '/../src/Models/Building.php';
require_once __DIR__ . '/../src/Models/Unit.php';

$response = ['success' => false, 'message' => '', 'data' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? '';

    try {
        switch ($form_type) {
            case 'provider':
                $provider = new Provider();
                $result = $provider->create([
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone']
                ]);
                $response = ['success' => true, 'message' => 'Provider created successfully', 'data' => ['id' => $result]];
                break;

            case 'building':
                $building = new Building();
                $details = [
                    'amenities' => array_map('trim', explode(',', $_POST['amenities'])),
                    'year_built' => (int)$_POST['year_built'],
                    'parking_spots' => (int)$_POST['parking_spots']
                ];
                
                $result = $building->create([
                    'provider_id' => $_POST['provider_id'],
                    'name' => $_POST['name'],
                    'address' => $_POST['address'],
                    'longitude' => (float)$_POST['longitude'],
                    'latitude' => (float)$_POST['latitude'],
                    'details' => $details
                ]);
                $response = ['success' => true, 'message' => 'Building created successfully', 'data' => ['id' => $result]];
                break;

            case 'unit':
                $unit = new Unit();
                $features = [
                    'ac' => isset($_POST['features']['ac']),
                    'flooring' => $_POST['features']['flooring'],
                    'appliances' => array_map('trim', explode(',', $_POST['features']['appliances']))
                ];

                $result = $unit->create([
                    'building_id' => $_POST['building_id'],
                    'unit_number' => $_POST['unit_number'],
                    'bedrooms' => (int)$_POST['bedrooms'],
                    'bathrooms' => (float)$_POST['bathrooms'],
                    'square_feet' => (int)$_POST['square_feet'],
                    'rent_amount' => (float)$_POST['rent_amount'],
                    'is_available' => isset($_POST['is_available']),
                    'available_from' => $_POST['available_from'],
                    'features' => $features
                ]);
                $response = ['success' => true, 'message' => 'Unit created successfully', 'data' => ['id' => $result]];
                break;

            default:
                throw new Exception('Invalid form type');
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
        error_log('Form Handler Error: ' . $e->getMessage());
    }
}

header('Content-Type: application/json');
echo json_encode($response);
