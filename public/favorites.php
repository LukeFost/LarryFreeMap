<?php
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';
$jwt = AuthMiddleware::requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>My Favorites</h1>
            <nav>
                <a href="map.php"><i class="fas fa-map"></i> Map</a>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="auth_handler.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </header>
        
        <main>
            <div class="property-grid">
                <!-- Will be populated by JavaScript -->
            </div>
        </main>
    </div>
    <script src="js/favorites.js"></script>
</body>
</html>
