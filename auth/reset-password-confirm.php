<?php
require_once '../includes/config.php';
require_once '../includes/db_handler.php';
require_once '../includes/csrf.php';

// Create a separate config for development/production
$env = getenv('APP_ENV') ?: 'development';
$config = require "config/{$env}.php";

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rate limiting check using constants
$attempts_window = ATTEMPTS_WINDOW;
$max_attempts = MAX_LOGIN_ATTEMPTS;

if (isset($_SESSION['confirm_attempts']) && isset($_SESSION['first_confirm_attempt'])) {
    if (time() - $_SESSION['first_confirm_attempt'] > $attempts_window) {
        $_SESSION['confirm_attempts'] = 0;
        $_SESSION['first_confirm_attempt'] = time();
    } elseif ($_SESSION['confirm_attempts'] >= $max_attempts) {
        die("Too many password reset attempts. Please try again later.");
    }
}

// Validate token parameter
$token = $_GET['token'] ?? '';
if (empty($token) || strlen($token) !== 64 || !ctype_xdigit($token)) {
    header("Location: ../student_hub/index.php");
    exit;
}

// Initialize token validation status
$token_valid = false;
$user_id = null;

try {
    executeInTransaction(function($pdo) use ($token, &$token_valid, &$user_id) {
        $stmt = $pdo->prepare("SELECT prt.* FROM password_reset_tokens prt WHERE prt.used = 0 AND prt.expires_at > NOW() ORDER BY prt.created_at DESC LIMIT 1");
        $stmt->execute();
        
        while ($row = $stmt->fetch()) {
            if (password_verify($token, $row['token'])) {
                $token_valid = true;
                $user_id = $row['user_id'];
                break;
            }
        }
    });

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
        // Validate CSRF token
        if (!verify_csrf_token($_POST['csrf_token'])) {
            die("Invalid CSRF token.");
        }

        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($password === $confirm_password) {
            try {
                executeInTransaction(function($pdo) use ($password, $user_id) {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$password_hash, $user_id]);

                    // Mark token as used
                    $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                });

                header("Location: ../student_hub/index.php");
                exit;
            } catch (Exception $e) {
                error_log("Password reset error: " . $e->getMessage());
                $error = "An error occurred while resetting your password. Please try again.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    }
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $error = "An error occurred. Please try again later.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        
        <?php if (!$token_valid): ?>
            <div class="error-message">
                Invalid or expired reset link. Please request a new password reset.
            </div>
            <div class="links">
                <a href="reset-password.php">Request New Reset Link</a>
                <a href="../student_hub/index.php">Back to Login</a>
            </div>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?token=' . urlencode($token)); ?>" id="resetForm">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$"
                           title="Must contain at least 12 characters, one uppercase, one lowercase, one number and one special character">
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" 
                           name="confirm_password" 
                           id="confirm_password" 
                           required>
                </div>

                <div class="form-group">
                    <button type="submit">Reset Password</button>
                </div>
            </form>

            <script>
            // Password strength checker
            document.getElementById('password').addEventListener('input', function() {
                const password = this.value;
                const strength = {
                    length: password.length >= 12,
                    lowercase: /[a-z]/.test(password),
                    uppercase: /[A-Z]/.test(password),
                    number: /\d/.test(password),
                    special: /[@$!%*?&]/.test(password)
                };
                
                const strengthDiv = document.getElementById('passwordStrength');
                let strengthText = 'Password must contain:<br>';
                strengthText += strength.length ? '✓' : '✗';
                strengthText += ' At least 12 characters<br>';
                strengthText += strength.lowercase ? '✓' : '✗';
                strengthText += ' Lowercase letter<br>';
                strengthText += strength.uppercase ? '✓' : '✗';
                strengthText += ' Uppercase letter<br>';
                strengthText += strength.number ? '✓' : '✗';
                strengthText += ' Number<br>';
                strengthText += strength.special ? '✓' : '✗';
                strengthText += ' Special character';
                
                strengthDiv.innerHTML = strengthText;
            });

            // Confirm password matcher
            document.getElementById('confirm_password').addEventListener('input', function() {
                if (this.value !== document.getElementById('password').value) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });

            // Prevent form resubmission
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            </script>
        <?php endif; ?>
    </div>
</body>
</html> 