// Initialize the map centered on Phoenix
const map = L.map('map').setView([33.4484, -112.0740], 13);
let userLocationMarker = null;

// Add OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Handle map clicks to show coordinates
map.on('click', function(e) {
    document.getElementById('lat').textContent = e.latlng.lat.toFixed(6);
    document.getElementById('lng').textContent = e.latlng.lng.toFixed(6);
});

// Function to show status messages
function showStatus(message, isError = false) {
    const status = document.getElementById('location-status');
    status.style.display = 'block';
    status.style.background = isError ? '#ffebee' : 'white';
    status.style.color = isError ? '#c62828' : 'black';
    status.textContent = message;
    
    if (!isError) {
        setTimeout(() => {
            status.style.display = 'none';
        }, 3000);
    }
}

// Function to get current location
function getCurrentLocation() {
    if (!navigator.geolocation) {
        showStatus('Geolocation is not supported by your browser', true);
        return;
    }

    showStatus('Getting your location...');

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            
            // Remove existing marker if it exists
            if (userLocationMarker) {
                map.removeLayer(userLocationMarker);
            }

            // Create a custom icon for user location
            const userIcon = L.divIcon({
                html: '📍',
                iconSize: [25, 25],
                className: 'user-location-icon'
            });

            // Add marker for user location
            userLocationMarker = L.marker([latitude, longitude], {
                icon: userIcon
            }).addTo(map);
            userLocationMarker.bindPopup('You are here!').openPopup();

            // Center map on user location
            map.setView([latitude, longitude], 15);
            
            showStatus('Location found!');
        },
        (error) => {
            let errorMessage = 'Unable to get your location';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Location permission denied';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Location information unavailable';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Location request timed out';
                    break;
            }
            showStatus(errorMessage, true);
        },
        {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
    );
}

// Function to load and display buildings
async function loadBuildings() {
    try {
        const response = await fetch('get_buildings.php');
        const buildings = await response.json();
        
        buildings.forEach(building => {
            const marker = L.marker([building.latitude, building.longitude])
                .addTo(map);
            
            const amenities = building.details?.amenities ? building.details.amenities.join(', ') : 'None';
            const yearBuilt = building.details?.year_built || 'Not specified';
            const parkingSpots = building.details?.parking_spots || 'Not specified';
            
            const popupContent = `
                <div class="popup-content">
                    <h3>${building.name}</h3>
                    <p><strong>Address:</strong> ${building.address}</p>
                    <p><strong>Year Built:</strong> ${yearBuilt}</p>
                    <p><strong>Parking Spots:</strong> ${parkingSpots}</p>
                    <p><strong>Amenities:</strong> ${amenities}</p>
                </div>
            `;
            
            marker.bindPopup(popupContent);
        });
    } catch (error) {
        console.error('Error loading buildings:', error);
    }
}

// Load buildings when the page loads
document.addEventListener('DOMContentLoaded', loadBuildings);
