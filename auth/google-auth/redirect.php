<?php
require_once '../../includes/config.php';
require_once '../../includes/db_handler.php';
require_once '../../vendor/autoload.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Generate and store state parameter to prevent CSRF
    $state = bin2hex(random_bytes(16));
    $_SESSION['google_auth_state'] = $state;

    // Create a new Google client instance with error handling
    $client = new Google_Client([
        'timeout' => 5, // 5 seconds timeout
        'retry' => [
            'retries' => 2
        ]
    ]);

    // Configure Google Client
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(BASE_URL . 'auth/google-auth/callback.php');
    
    // Add required scopes
    $client->addScope("email");
    $client->addScope("profile");
    
    // Set state parameter
    $client->setState($state);

    // Set access type to offline to get refresh token
    $client->setAccessType('offline');
    
    // Force approval prompt to ensure getting refresh token
    $client->setPrompt('consent');

    // Log the authentication attempt
    if (isset($_SESSION['user_id'])) {
        executeInTransaction(function($pdo) {
            $stmt = $pdo->prepare("INSERT INTO auth_logs (user_id, auth_type, status, ip_address) VALUES (?, 'google', 'redirect', ?)");
            $stmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);
        });
    }

    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Set security headers
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    // Redirect to Google's OAuth 2.0 server
    header('Location: ' . $client->createAuthUrl());
    exit;

} catch (Exception $e) {
    error_log('Google Auth Redirect Error: ' . $e->getMessage());
    $_SESSION['auth_error'] = 'Failed to initialize Google authentication. Please try again later.';
    header('Location: ../index.php');
    exit;
}
?>
