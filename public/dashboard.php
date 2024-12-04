<?php
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';
$jwt = AuthMiddleware::requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>My Dashboard</h1>
            <nav>
                <a href="map.php"><i class="fas fa-map"></i> Map</a>
                <a href="favorites.php"><i class="fas fa-heart"></i> Favorites</a>
                <a href="auth_handler.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </header>
        
        <main>
            <section class="recent-views">
                <h2>Recently Viewed Properties</h2>
                <div class="property-grid">
                    <!-- Will be populated by JavaScript -->
                </div>
            </section>
            
            <section class="saved-searches">
                <h2>Saved Searches</h2>
                <div class="search-list">
                    <!-- Will be populated by JavaScript -->
                </div>
            </section>
        </main>
    </div>
    <script src="js/dashboard.js"></script>
</body>
</html>
