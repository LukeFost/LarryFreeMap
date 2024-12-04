<?php
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
        }
    }
}

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Models/Provider.php';
require_once __DIR__ . '/../src/Models/Building.php';
require_once __DIR__ . '/../src/Models/Unit.php';

// Error handling
function handleError($e) {
    echo "Error: " . $e->getMessage() . "\n";
    die();
}

try {
    // Test Provider CRUD
    echo "Testing Provider CRUD Operations:\n";
    echo "--------------------------------\n";
    
    $provider = new Provider();
    
    // Create
    $providerData = [
        'name' => 'Sample Property Management',
        'email' => 'contact@sample.com',
        'phone' => '555-0123'
    ];
    
    $providerId = $provider->create($providerData);
    echo "Created provider with ID: $providerId\n";
    
    // Read
    $fetchedProvider = $provider->getById($providerId);
    echo "Fetched provider: " . json_encode($fetchedProvider, JSON_PRETTY_PRINT) . "\n";
    
    // Update
    $updateProviderData = [
        'name' => 'Updated Property Management',
        'phone' => '555-9876'
    ];
    $provider->update($providerId, $updateProviderData);
    $updatedProvider = $provider->getById($providerId);
    echo "Updated provider: " . json_encode($updatedProvider, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test Building CRUD
    echo "Testing Building CRUD Operations:\n";
    echo "--------------------------------\n";
    
    $building = new Building();
    
    // Create
    $buildingData = [
        'provider_id' => $providerId,
        'name' => 'Sunset Apartments',
        'address' => '123 Sunset Blvd, Los Angeles, CA 90028',
        'latitude' => 34.098907,
        'longitude' => -118.327759,
        'details' => [
            'year_built' => 1985,
            'parking_spots' => 50,
            'amenities' => ['pool', 'gym', 'parking']
        ]
    ];
    
    $buildingId = $building->create($buildingData);
    echo "Created building with ID: $buildingId\n";
    
    // Read
    $fetchedBuilding = $building->getById($buildingId);
    echo "Fetched building: " . json_encode($fetchedBuilding, JSON_PRETTY_PRINT) . "\n";
    
    // Update
    $updateBuildingData = [
        'name' => 'Sunset Luxury Apartments',
        'details' => [
            'year_built' => 1985,
            'parking_spots' => 60,
            'amenities' => ['pool', 'gym', 'parking', 'sauna']
        ]
    ];
    $building->update($buildingId, $updateBuildingData);
    $updatedBuilding = $building->getById($buildingId);
    echo "Updated building: " . json_encode($updatedBuilding, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test Unit CRUD
    echo "Testing Unit CRUD Operations:\n";
    echo "--------------------------------\n";
    
    $unit = new Unit();
    
    // Create
    $unitData = [
        'building_id' => $buildingId,
        'unit_number' => '101',
        'bedrooms' => 2,
        'bathrooms' => 1,
        'square_feet' => 850,
        'rent_amount' => 2500.00,
        'is_available' => true,
        'available_from' => '2024-02-01',
        'features' => [
            'appliances' => ['dishwasher', 'washer/dryer'],
            'ac' => true,
            'flooring' => 'hardwood'
        ]
    ];
    
    $unitId = $unit->create($unitData);
    echo "Created unit with ID: $unitId\n";
    
    // Read
    $fetchedUnit = $unit->getById($unitId);
    echo "Fetched unit: " . json_encode($fetchedUnit, JSON_PRETTY_PRINT) . "\n";
    
    // Update
    $updateUnitData = [
        'rent_amount' => 2600.00,
        'features' => [
            'appliances' => ['dishwasher', 'washer/dryer', 'refrigerator'],
            'ac' => true,
            'flooring' => 'hardwood',
            'view' => 'city'
        ]
    ];
    $unit->update($unitId, $updateUnitData);
    $updatedUnit = $unit->getById($unitId);
    echo "Updated unit: " . json_encode($updatedUnit, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test searching for nearby buildings
    echo "Testing Nearby Buildings Search:\n";
    echo "--------------------------------\n";
    $nearbyBuildings = $building->findNearby(34.098907, -118.327759, 5000);
    echo "Found " . count($nearbyBuildings) . " nearby buildings\n";
    
    // Test unit search
    echo "\nTesting Unit Search:\n";
    echo "--------------------------------\n";
    $searchCriteria = [
        'min_bedrooms' => 2,
        'max_rent' => 3000,
        'min_square_feet' => 800
    ];
    $searchResults = $unit->searchUnits($searchCriteria);
    echo "Found " . count($searchResults) . " units matching criteria\n\n";
    
    // Test Delete Operations
    echo "Testing Delete Operations:\n";
    echo "--------------------------------\n";
    
    // Delete unit first (due to foreign key constraints)
    echo "Deleting unit with ID: $unitId\n";
    $unit->delete($unitId);
    $deletedUnit = $unit->getById($unitId);
    echo "Unit deleted: " . ($deletedUnit ? "No" : "Yes") . "\n";
    
    // Delete building
    echo "Deleting building with ID: $buildingId\n";
    $building->delete($buildingId);
    $deletedBuilding = $building->getById($buildingId);
    echo "Building deleted: " . ($deletedBuilding ? "No" : "Yes") . "\n";
    
    // Delete provider
    echo "Deleting provider with ID: $providerId\n";
    $provider->delete($providerId);
    $deletedProvider = $provider->getById($providerId);
    echo "Provider deleted: " . ($deletedProvider ? "No" : "Yes") . "\n";
    
    echo "\nAll tests completed successfully!\n";
    
} catch (Exception $e) {
    handleError($e);
}
