<?php
class AuthMiddleware {
    public static function requireAuth() {
        session_start();
        if (!isset($_SESSION['jwt'])) {
            header('Location: /login.php');
            exit;
        }
        return $_SESSION['jwt'];
    }
    
    public static function requireRole($role) {
        $jwt = self::requireAuth();
        $auth = Auth::getInstance();
        
        if (!$auth->verifyRole($role, $jwt)) {
            header('Location: /unauthorized.php');
            exit;
        }
    }
}
