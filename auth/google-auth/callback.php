<?php
require_once '../../includes/config.php';
require_once '../../includes/db_handler.php';
require_once '../../vendor/autoload.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Verify state parameter to prevent CSRF
    if (!isset($_GET['state']) || !isset($_SESSION['google_auth_state']) || 
        $_GET['state'] !== $_SESSION['google_auth_state']) {
        throw new Exception('Invalid state parameter. Possible CSRF attack.');
    }

    // Clear the state from session
    unset($_SESSION['google_auth_state']);

    // Initialize Google Client
    $client = new Google_Client([
        'timeout' => 5,
        'retry' => ['retries' => 2]
    ]);
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(BASE_URL . 'auth/google-auth/callback.php');

    if (!isset($_GET['code'])) {
        throw new Exception('Authorization code not received.');
    }

    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (!isset($token['access_token'])) {
        throw new Exception('Failed to obtain access token.');
    }

    $client->setAccessToken($token['access_token']);
    
    // Get user information
    $oauth2 = new Google_Service_Oauth2($client);
    $google_user = $oauth2->userinfo->get();
    
    executeInTransaction(function($pdo) use ($google_user, $token) {
        // Validate and sanitize user data
        $email = filter_var($google_user->email, FILTER_SANITIZE_EMAIL);
        $name = htmlspecialchars($google_user->name);
        $google_id = $google_user->id;
        $picture = filter_var($google_user->picture, FILTER_SANITIZE_URL);

        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Create a new user if not exists
            $username = generateUniqueUsername($email, $pdo);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$username, $email, password_hash($google_id, PASSWORD_DEFAULT)]);
            $user_id = $pdo->lastInsertId();
        } else {
            $user_id = $user['id'];
        }

        // Log the successful authentication
        $stmt = $pdo->prepare("INSERT INTO auth_logs 
            (user_id, auth_type, status, ip_address, user_agent) 
            VALUES (?, 'google', 'success', ?, ?)");
        $stmt->execute([
            $user_id, 
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);

        // Set session variables
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['last_activity'] = time();
        
        // Set secure session cookie parameters
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Lax');
        }

        // Redirect to dashboard
        header("Location: ../../dashboard/index.php");
        exit;
    });

} catch (Google_Service_Exception $e) {
    error_log('Google Service Exception: ' . $e->getMessage());
    logAuthError('google', 'service_error', $e->getMessage());
    $_SESSION['auth_error'] = 'An error occurred with Google services. Please try again later.';
    header('Location: ../index.php');
    exit;
} catch (Google_Exception $e) {
    error_log('Google Client Exception: ' . $e->getMessage());
    logAuthError('google', 'client_error', $e->getMessage());
    $_SESSION['auth_error'] = 'Failed to complete Google authentication. Please try again later.';
    header('Location: ../index.php');
    exit;
} catch (Exception $e) {
    error_log("Google Auth Error: " . $e->getMessage());
    $_SESSION['auth_error'] = 'Authentication failed. Please try again.';
    header('Location: ../index.php');
    exit;
}

// Helper function to generate unique username
function generateUniqueUsername($email, $pdo) {
    $base = strtolower(explode('@', $email)[0]);
    $username = $base;
    $counter = 1;
    
    while (true) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if (!$stmt->fetch()) {
            return $username;
        }
        $username = $base . $counter;
        $counter++;
    }
}

// Helper function to log authentication errors
function logAuthError($type, $status, $error_message) {
    try {
        executeInTransaction(function($pdo) use ($type, $status, $error_message) {
            $stmt = $pdo->prepare("INSERT INTO auth_logs 
                (auth_type, status, error_message, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $type,
                $status,
                $error_message,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
        });
    } catch (Exception $e) {
        error_log('Failed to log auth error: ' . $e->getMessage());
    }
}
?>