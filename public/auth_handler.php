<?php
require_once __DIR__ . '/../src/Auth.php';

header('Content-Type: application/json');
session_start();

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    $auth = Auth::getInstance();
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'register':
            $result = $auth->signUp(
                $_POST['email'],
                $_POST['password'],
                $_POST['role']
            );
            $_SESSION['jwt'] = $result['access_token'];
            $_SESSION['user'] = $result['user'];
            $response = [
                'success' => true,
                'message' => 'Registration successful',
                'data' => ['redirect' => 'map.php']
            ];
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
