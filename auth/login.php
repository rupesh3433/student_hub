<?php
require_once '../includes/config.php';
require_once '../includes/db_handler.php';
require_once '../includes/csrf.php';

// Start the session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a registration message exists (set by register.php)
$registrationMessage = "";
if (isset($_SESSION['registration_message'])) {
    $registrationMessage = $_SESSION['registration_message'];
    unset($_SESSION['registration_message']);
}

// Rate limiting check using constants for login
$attempts_window = ATTEMPTS_WINDOW;
$max_attempts = MAX_LOGIN_ATTEMPTS;

if (isset($_SESSION['login_attempts']) && isset($_SESSION['first_attempt_time'])) {
    if (time() - $_SESSION['first_attempt_time'] > $attempts_window) {
        // Reset attempts if the window has expired
        $_SESSION['login_attempts'] = 0;
        $_SESSION['first_attempt_time'] = time();
    } elseif ($_SESSION['login_attempts'] >= $max_attempts) {
        die("Too many login attempts. Please try again later.");
    }
}

$error = ""; // For login errors

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token.");
    }
    
    // Retrieve and sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Initialize login attempts tracking if not already set
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['first_attempt_time'] = time();
        }

        try {
            executeInTransaction(function($pdo) use ($username, $password) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Credentials are valid; regenerate session and set user details
                    session_regenerate_id(true); // Prevent session fixation
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['last_activity'] = time();
                    
                    // Reset login attempts
                    $_SESSION['login_attempts'] = 0;
                    
                    // Set secure cookie flags if using HTTPS
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                        ini_set('session.cookie_secure', 1);
                        ini_set('session.cookie_httponly', 1);
                        ini_set('session.cookie_samesite', 'Lax');
                    }
                    
                    // Handle "remember me" functionality
                    if (isset($_POST['remember_me'])) {
                        $token = bin2hex(random_bytes(32));
                        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                        
                        // Store token in database
                        $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
                        $stmt->execute([$user['id'], password_hash($token, PASSWORD_DEFAULT), date('Y-m-d H:i:s', $expiry)]);
                        
                        // Set remember me cookie
                        setcookie('remember_token', $token, $expiry, '/', '', isset($_SERVER['HTTPS']), true);
                    }
                    
                    // Log successful login
                    $stmt = $pdo->prepare("INSERT INTO login_logs (user_id, status, ip_address) VALUES (?, 'success', ?)");
                    $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);
                    
                    header("Location: ../dashboard/dashboard.php");
                    exit;
                }
                throw new Exception("Invalid credentials");
            });
        } catch (Exception $e) {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['first_attempt_time'] = $_SESSION['first_attempt_time'] ?? time();
            $error = "Invalid username or password.";
            
            // Log failed attempt
            executeInTransaction(function($pdo) use ($username) {
                $stmt = $pdo->prepare("INSERT INTO login_logs (username, status, ip_address) VALUES (?, 'failed', ?)");
                $stmt->execute([$username, $_SERVER['REMOTE_ADDR']]);
            });
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Student Hub</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/auth.css">
    <style>
        /* Style for the registration notification message */
        #registration-message {
            background-color: #ddffdd;
            color: #006600;
            padding: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
            margin-bottom: 20px;
            border: 1px solid #aaffaa;
        }
    </style>
</head>
<body>
    <!-- Display registration message if it exists (from register.php) -->
    <?php if (!empty($registrationMessage)): ?>
        <div id="registration-message"><?php echo htmlspecialchars($registrationMessage); ?></div>
        <script>
            // Hide the registration message after 3 seconds
            setTimeout(function(){
                var msgDiv = document.getElementById("registration-message");
                if (msgDiv) {
                    msgDiv.style.display = "none";
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label>
                    <input type="checkbox" name="remember_me"> Remember me
                </label>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>
        <div class="auth-links">
    <a href="../index.php">Register</a>
    <a href="reset-password-confirm.php">Forgot Password?</a>
</div>

        </p>
        <p>
            <a href="auth/google-auth/redirect.php">Login with Google</a>
        </p>
    </div>
</body>
</html>
