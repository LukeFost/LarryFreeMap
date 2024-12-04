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

function logDebug($message, $data = null) {
    $logMessage = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data !== null) {
        $logMessage .= ' - Data: ' . print_r($data, true);
    }
    error_log($logMessage);
}

// Log incoming request and environment variables
logDebug('Received auth request', $_POST);
logDebug('Environment check', [
    'SUPABASE_URL' => getenv('SUPABASE_URL') ?: 'Not set',
    'SUPABASE_ANON_KEY' => getenv('SUPABASE_ANON_KEY') ? 'Set (hidden)' : 'Not set'
]);

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    $auth = Auth::getInstance();
    $action = $_POST['action'] ?? '';
    
    logDebug('Processing action', $action);

    switch ($action) {
        case 'register':
            if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['role'])) {
                throw new Exception('Missing required registration fields');
            }
            
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            if (strlen($_POST['password']) < 6) {
                throw new Exception('Password must be at least 6 characters long');
            }
            
            logDebug('Attempting registration', ['email' => $_POST['email'], 'role' => $_POST['role']]);
            
            $result = $auth->signUp(
                $_POST['email'],
                $_POST['password'],
                $_POST['role']
            );
            
            logDebug('Registration result received', $result);
            
            if (isset($result['id'])) {
                // After successful registration, perform sign in to get the access token
                logDebug('Registration successful, attempting auto-login');
                
                try {
                    $signInResult = $auth->signIn($_POST['email'], $_POST['password']);
                    logDebug('Auto-login result', $signInResult);
                    
                    if (isset($signInResult['access_token'])) {
                        $_SESSION['jwt'] = $signInResult['access_token'];
                        $_SESSION['user'] = $result;
                        $response = [
                            'success' => true,
                            'message' => 'Registration and login successful',
                            'data' => ['redirect' => 'map.php']
                        ];
                    } else {
                        throw new Exception('Auto-login failed: No access token received');
                    }
                } catch (Exception $e) {
                    logDebug('Auto-login failed', ['error' => $e->getMessage()]);
                    throw new Exception('Registration successful but auto-login failed. Please try logging in manually.');
                }
            } else {
                throw new Exception('Invalid registration response: No user ID received');
            }
            break;

        case 'login':
            if (!isset($_POST['email']) || !isset($_POST['password'])) {
                throw new Exception('Email and password are required');
            }
            
            logDebug('Attempting login', ['email' => $_POST['email']]);
            
            $result = $auth->signIn(
                $_POST['email'],
                $_POST['password']
            );
            
            logDebug('Login result received', $result);
            
            if (isset($result['access_token'])) {
                $_SESSION['jwt'] = $result['access_token'];
                $_SESSION['user'] = $result['user'];
                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => ['redirect' => 'map.php']
                ];
            } else {
                throw new Exception('Invalid login response: No access token received');
            }
            break;

        case 'logout':
            logDebug('Attempting logout');
            
            if (isset($_SESSION['jwt'])) {
                $auth->signOut($_SESSION['jwt']);
                session_destroy();
                $response = [
                    'success' => true,
                    'message' => 'Logout successful',
                    'data' => ['redirect' => 'login.php']
                ];
                logDebug('Logout successful');
            } else {
                logDebug('Logout called with no active session');
                $response = [
                    'success' => true,
                    'message' => 'No active session',
                    'data' => ['redirect' => 'login.php']
                ];
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    logDebug('Error occurred', [
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Map common error codes to user-friendly messages
    $userMessage = match($e->getMessage()) {
        'Email not confirmed' => 'Please check your email and confirm your account before logging in.',
        'Invalid login credentials' => 'Invalid email or password. Please try again.',
        'Email already registered' => 'An account with this email already exists.',
        default => $e->getMessage()
    };
    
    $response['message'] = $userMessage;
    http_response_code($e->getCode() ?: 400);
}

echo json_encode($response);
