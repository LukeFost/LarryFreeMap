<?php
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';
AuthMiddleware::requireRole('admin');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="nav-links">
                <li class="active" data-tab="overview">
                    <i class="fas fa-chart-line"></i> Overview
                </li>
                <li data-tab="providers">
                    <i class="fas fa-users"></i> Providers
                </li>
                <li data-tab="buildings">
                    <i class="fas fa-building"></i> Buildings
                </li>
                <li data-tab="units">
                    <i class="fas fa-door-open"></i> Units
                </li>
            </ul>
            <div class="sidebar-footer">
                <button onclick="location.href='../auth_handler.php?action=logout'" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </nav>

        <main class="content">
            <div id="overview" class="tab-content active">
                <h2>Dashboard Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Providers</h3>
                        <p id="provider-count">Loading...</p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Buildings</h3>
                        <p id="building-count">Loading...</p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Units</h3>
                        <p id="unit-count">Loading...</p>
                    </div>
                    <div class="stat-card">
                        <h3>Available Units</h3>
                        <p id="available-units">Loading...</p>
                    </div>
                </div>
            </div>

            <div id="providers" class="tab-content">
                <h2>Providers Management</h2>
                <div class="table-container">
                    <table id="providers-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Buildings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="buildings" class="tab-content">
                <h2>Buildings Management</h2>
                <div class="table-container">
                    <table id="buildings-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Provider</th>
                                <th>Units</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="units" class="tab-content">
                <h2>Units Management</h2>
                <div class="table-container">
                    <table id="units-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Building</th>
                                <th>Unit Number</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button id="confirmDelete" class="delete-btn">Delete</button>
                <button id="cancelDelete" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>
