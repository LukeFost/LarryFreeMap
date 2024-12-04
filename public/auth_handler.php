<?php
require_once __DIR__ . '/../src/Auth.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
session_start();

// Log incoming request
error_log('Received auth request. POST data: ' . print_r($_POST, true));

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
