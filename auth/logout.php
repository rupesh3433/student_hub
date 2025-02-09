<?php
require_once '../includes/config.php';
require_once '../includes/db_handler.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the logout action if user was logged in
if (isset($_SESSION['user_id'])) {
    try {
        executeInTransaction(function($pdo) {
            // Log logout action
            $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action, ip_address) VALUES (?, 'logout', ?)");
            $stmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);

            // Remove any remember-me tokens
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        });
    } catch (Exception $e) {
        error_log("Logout logging failed: " . $e->getMessage());
    }
}

// Remove remember-me cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/', '', true, true);
}

// Destroy the session
session_destroy();

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Redirect to login page with security headers
header("Clear-Site-Data: \"cache\", \"cookies\", \"storage\"");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Location: ../index.php");
exit();
?>
