<?php
class Auth {
    private static $instance = null;
    private $supabaseUrl;
    private $supabaseKey;
    
    private function __construct() {
        $this->supabaseUrl = getenv('SUPABASE_URL');
        $this->supabaseKey = getenv('SUPABASE_ANON_KEY');
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function makeRequest($endpoint, $method, $data = null) {
        $ch = curl_init();
        $url = $this->supabaseUrl . '/auth/v1/' . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 400) {
            throw new Exception($responseData['message'] ?? 'Authentication error');
        }
        
        return $responseData;
    }
    
    public function signUp($email, $password, $role = 'user') {
        $userData = [
            'email' => $email,
            'password' => $password,
            'data' => ['role' => $role]
        ];
        
        return $this->makeRequest('signup', 'POST', $userData);
    }
    
    public function signIn($email, $password) {
        $userData = [
            'email' => $email,
            'password' => $password
        ];
        
        return $this->makeRequest('token?grant_type=password', 'POST', $userData);
    }
    
    public function signOut($jwt) {
        $headers = ['Authorization: Bearer ' . $jwt];
        return $this->makeRequest('logout', 'POST', null);
    }
    
    public function getCurrentUser($jwt) {
        if (!$jwt) {
            return null;
        }
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->supabaseUrl . '/auth/v1/user');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $jwt
            ]);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response, true);
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function verifyRole($requiredRole, $jwt) {
        $user = $this->getCurrentUser($jwt);
        if (!$user) {
            return false;
        }
        return $user['user_metadata']['role'] === $requiredRole;
    }
}
