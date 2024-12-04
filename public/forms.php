<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management CRUD</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-section { margin-bottom: 30px; padding: 20px; border: 1px solid #ccc; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { 
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .map-link {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .map-link:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h1>Property Management System</h1>
    
    <a href="map.php" class="map-link">View Properties Map</a>

    <!-- Provider Form -->
    <div class="form-section">
        <h2>Provider Management</h2>
        <form id="providerForm" class="crud-form">
            <input type="hidden" name="form_type" value="provider">
            <div class="form-group">
                <label for="providerName">Provider Name:</label>
                <input type="text" id="providerName" name="name" required>
            </div>
            <div class="form-group">
                <label for="providerEmail">Email:</label>
                <input type="email" id="providerEmail" name="email" required>
            </div>
            <div class="form-group">
                <label for="providerPhone">Phone:</label>
                <input type="tel" id="providerPhone" name="phone" required>
            </div>
            <button type="submit">Create Provider</button>
            <div class="result" style="display: none;"></div>
        </form>
    </div>

    <!-- Building Form -->
    <div class="form-section">
        <h2>Building Management</h2>
        <form id="buildingForm" class="crud-form">
            <input type="hidden" name="form_type" value="building">
            <div class="form-group">
                <label for="buildingProviderId">Provider ID:</label>
                <input type="text" id="buildingProviderId" name="provider_id" required>
            </div>
            <div class="form-group">
                <label for="buildingName">Building Name:</label>
                <input type="text" id="buildingName" name="name" required>
            </div>
            <div class="form-group">
                <label for="buildingAddress">Address:</label>
                <input type="text" id="buildingAddress" name="address" required>
            </div>
            <div class="form-group">
                <label for="buildingLongitude">Longitude:</label>
                <input type="number" id="buildingLongitude" name="longitude" step="any" required>
            </div>
            <div class="form-group">
                <label for="buildingLatitude">Latitude:</label>
                <input type="number" id="buildingLatitude" name="latitude" step="any" required>
            </div>
            <div class="form-group">
                <a href="map.php" target="_blank" class="map-link">Use Map to Select Location</a>
            </div>
            <div class="form-group">
                <label for="buildingAmenities">Amenities (comma-separated):</label>
                <input type="text" id="buildingAmenities" name="amenities" placeholder="pool, gym, parking">
            </div>
            <div class="form-group">
                <label for="buildingYearBuilt">Year Built:</label>
                <input type="number" id="buildingYearBuilt" name="year_built" required>
            </div>
            <div class="form-group">
                <label for="buildingParkingSpots">Parking Spots:</label>
                <input type="number" id="buildingParkingSpots" name="parking_spots" required>
            </div>
            <button type="submit">Create Building</button>
            <div class="result" style="display: none;"></div>
        </form>
    </div>

    <!-- Unit Form -->
    <div class="form-section">
        <h2>Unit Management</h2>
        <form id="unitForm" class="crud-form">
            <input type="hidden" name="form_type" value="unit">
            <div class="form-group">
                <label for="unitBuildingId">Building ID:</label>
                <input type="text" id="unitBuildingId" name="building_id" required>
            </div>
            <div class="form-group">
                <label for="unitNumber">Unit Number:</label>
                <input type="text" id="unitNumber" name="unit_number" required>
            </div>
            <div class="form-group">
                <label for="unitBedrooms">Bedrooms:</label>
                <input type="number" id="unitBedrooms" name="bedrooms" required>
            </div>
            <div class="form-group">
                <label for="unitBathrooms">Bathrooms:</label>
                <input type="number" id="unitBathrooms" name="bathrooms" step="0.5" required>
            </div>
            <div class="form-group">
                <label for="unitSquareFeet">Square Feet:</label>
                <input type="number" id="unitSquareFeet" name="square_feet" required>
            </div>
            <div class="form-group">
                <label for="unitRentAmount">Rent Amount:</label>
                <input type="number" id="unitRentAmount" name="rent_amount" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="unitIsAvailable">Available:</label>
                <input type="checkbox" id="unitIsAvailable" name="is_available" value="1">
            </div>
            <div class="form-group">
                <label for="unitAvailableFrom">Available From:</label>
                <input type="date" id="unitAvailableFrom" name="available_from" required>
            </div>
            <div class="form-group">
                <label for="unitFeatures">Features:</label>
                <div>
                    <input type="checkbox" id="unitAC" name="features[ac]" value="1">
                    <label for="unitAC">AC</label>
                </div>
                <div>
                    <label for="unitFlooring">Flooring:</label>
                    <input type="text" id="unitFlooring" name="features[flooring]">
                </div>
                <div>
                    <label for="unitAppliances">Appliances (comma-separated):</label>
                    <input type="text" id="unitAppliances" name="features[appliances]" placeholder="dishwasher, washer/dryer">
                </div>
            </div>
            <button type="submit">Create Unit</button>
            <div class="result" style="display: none;"></div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.crud-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const resultDiv = this.querySelector('.result');
                    
                    try {
                        const response = await fetch('form_handler.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        resultDiv.textContent = result.message;
                        resultDiv.className = 'result ' + (result.success ? 'success' : 'error');
                        resultDiv.style.display = 'block';
                        
                        if (result.success) {
                            // Clear the form on success
                            this.reset();
                            
                            // Show the created entity's ID
                            if (result.data && result.data.id) {
                                resultDiv.textContent += ` (ID: ${result.data.id})`;
                            }
                        }
                    } catch (error) {
                        resultDiv.textContent = 'An error occurred while processing the request.';
                        resultDiv.className = 'result error';
                        resultDiv.style.display = 'block';
                    }
                });
            });
        });
    </script>
</body>
</html>
