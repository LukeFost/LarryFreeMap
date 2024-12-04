<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="css/map.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div id="map"></div>
    <button class="location-button" onclick="getCurrentLocation()">📍 My Location</button>
    <div id="location-status"></div>
    <div class="nav-box">
        <div class="user-profile">
            <i class="fas fa-user-circle"></i>
            <span id="user-email">Loading...</span>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="favorites.php"><i class="fas fa-heart"></i> Favorites</a>
            <a href="auth_handler.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
    <div class="coordinates-box">
        <p>Click anywhere on the map to get coordinates</p>
        <p>Latitude: <span id="lat">-</span></p>
        <p>Longitude: <span id="lng">-</span></p>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/map.js"></script>
</body>
</html>
