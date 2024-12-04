<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="css/map.css" />
</head>
<body>
    <div id="map"></div>
    <button class="location-button" onclick="getCurrentLocation()">📍 My Location</button>
    <div id="location-status"></div>
    <div class="coordinates-box">
        <p>Click anywhere on the map to get coordinates</p>
        <p>Latitude: <span id="lat">-</span></p>
        <p>Longitude: <span id="lng">-</span></p>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/map.js"></script>
</body>
</html>
