<?php
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Centralized session configuration
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_secure' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'gc_maxlifetime' => $_ENV['SESSION_LIFETIME'] ?? 1800,
            'use_strict_mode' => true
        ]);
    }
}

// Initialize session by default
initSession();

// Constants
define('BASE_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/student_hub/');
define('UPLOAD_DIR', __DIR__ . '/../dashboard/files/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB

// Replace with your actual Google OAuth credentials if using them.
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');

// Error logging configuration
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Rate limiting configuration
define('MAX_LOGIN_ATTEMPTS', $_ENV['MAX_LOGIN_ATTEMPTS'] ?? 5);
define('ATTEMPTS_WINDOW', $_ENV['ATTEMPTS_WINDOW'] ?? 900);
?>
