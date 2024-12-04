<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Property Management</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Register</h2>
        <form id="registerForm" action="auth_handler.php" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="provider">Provider</option>
                </select>
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
        <div id="message" class="message"></div>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>
