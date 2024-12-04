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
        
        error_log("Making request to Supabase: $method $url");
        if ($data) {
            error_log("Request data: " . json_encode($data));
        }
        
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
        
        // Add verbose debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Log verbose output
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("Curl verbose output: " . $verboseLog);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            error_log("Curl error: " . $error);
            curl_close($ch);
            throw new Exception($error);
        }
        
        error_log("Response status code: " . $httpCode);
        error_log("Response body: " . $response);
        
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            throw new Exception("Invalid JSON response from server");
        }
        
        if ($httpCode >= 400) {
            $errorMessage = isset($responseData['error']) ? 
                           $responseData['error']['message'] : 
                           ($responseData['message'] ?? 'Authentication error');
            error_log("Supabase error: " . $errorMessage);
            throw new Exception($errorMessage);
        }
        
        return $responseData;
    }
    
    public function signUp($email, $password, $role = 'user') {
        if (empty($email) || empty($password)) {
            throw new Exception('Email and password are required');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters long');
        }
        
        $userData = [
            'email' => $email,
            'password' => $password,
            'data' => ['role' => $role]
        ];
        
        error_log("Attempting signup for email: $email with role: $role");
        
        try {
            $result = $this->makeRequest('signup', 'POST', $userData);
            error_log("Signup successful for: $email");
            return $result;
        } catch (Exception $e) {
            error_log("Signup failed for $email: " . $e->getMessage());
            throw $e;
        }
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
