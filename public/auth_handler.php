<?php
// Enable error reporting
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

require_once __DIR__ . '/../src/Auth.php';

header('Content-Type: application/json');
session_start();

// Log incoming request and environment variables
error_log('Received auth request. POST data: ' . print_r($_POST, true));
error_log('Environment check - SUPABASE_URL: ' . (getenv('SUPABASE_URL') ?: 'Not set'));
error_log('Environment check - SUPABASE_ANON_KEY: ' . (getenv('SUPABASE_ANON_KEY') ? 'Set (hidden)' : 'Not set'));

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    $auth = Auth::getInstance();
    $action = $_POST['action'] ?? '';
    
    error_log('Processing action: ' . $action);

    switch ($action) {
        case 'register':
            if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['role'])) {
                throw new Exception('Missing required registration fields');
            }
            
            error_log('Attempting registration for email: ' . $_POST['email']);
            
            $result = $auth->signUp(
                $_POST['email'],
                $_POST['password'],
                $_POST['role']
            );
            
            error_log('Registration result: ' . print_r($result, true));
            
            if (isset($result['access_token'])) {
                $_SESSION['jwt'] = $result['access_token'];
                $_SESSION['user'] = $result['user'];
                $response = [
                    'success' => true,
                    'message' => 'Registration successful',
                    'data' => ['redirect' => 'map.php']
                ];
            } else {
                throw new Exception('Invalid registration response');
            }
            break;

        case 'login':
            $result = $auth->signIn(
                $_POST['email'],
                $_POST['password']
            );
            $_SESSION['jwt'] = $result['access_token'];
            $_SESSION['user'] = $result['user'];
            $response = [
                'success' => true,
                'message' => 'Login successful',
                'data' => ['redirect' => 'map.php']
            ];
            break;

        case 'logout':
            if (isset($_SESSION['jwt'])) {
                $auth->signOut($_SESSION['jwt']);
                session_destroy();
                $response = [
                    'success' => true,
                    'message' => 'Logout successful',
                    'data' => ['redirect' => 'login.php']
                ];
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
