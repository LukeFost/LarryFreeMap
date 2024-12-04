<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        
        try {
            // Ensure we have a valid port number
            $port = intval($config['supabase']['port']);
            if ($port <= 0) {
                $port = 6543; // Default Supabase port
            }
            
            $dsn = sprintf(
                "pgsql:host=%s;dbname=%s;port=%d;sslmode=require",
                $config['supabase']['host'],
                $config['supabase']['database'],
                $port
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5
            ];
            
            $this->connection = new PDO(
                $dsn,
                $config['supabase']['user'],
                $config['supabase']['password'],
                $options
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage() . "\nDSN: " . $dsn);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
