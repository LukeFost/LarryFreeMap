<?php
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

echo "Testing Supabase Environment Variables:\n";
echo "SUPABASE_URL: " . (getenv('SUPABASE_URL') ?: 'Not set') . "\n";
echo "SUPABASE_ANON_KEY: " . (getenv('SUPABASE_ANON_KEY') ? 'Set (hidden)' : 'Not set') . "\n";

// Test .env file loading
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "\n.env file exists\n";
    $contents = file_get_contents($envFile);
    if ($contents === false) {
        echo "Error reading .env file\n";
    } else {
        echo "Number of lines in .env: " . substr_count($contents, "\n") . "\n";
        
        // Check for specific variables (without showing values)
        $hasSupabaseUrl = strpos($contents, 'SUPABASE_URL') !== false;
        $hasSupabaseKey = strpos($contents, 'SUPABASE_ANON_KEY') !== false;
        
        echo "SUPABASE_URL declaration found: " . ($hasSupabaseUrl ? 'Yes' : 'No') . "\n";
        echo "SUPABASE_ANON_KEY declaration found: " . ($hasSupabaseKey ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "\n.env file not found at: $envFile\n";
}
