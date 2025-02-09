<?php
require_once 'includes/config.php';
require_once 'includes/db_handler.php';
require_once 'includes/csrf.php';

// Start the session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a registration message exists (set by a previous POST)
$registrationMessage = "";
if (isset($_SESSION['registration_message'])) {
    $registrationMessage = $_SESSION['registration_message'];
    unset($_SESSION['registration_message']);
}

// Rate limiting check for registration attempts
$attempts_window = ATTEMPTS_WINDOW;
$max_attempts = MAX_LOGIN_ATTEMPTS;
if (isset($_SESSION['register_attempts']) && isset($_SESSION['first_attempt_time'])) {
    if (time() - $_SESSION['first_attempt_time'] > $attempts_window) {
        $_SESSION['register_attempts'] = 0;
        $_SESSION['first_attempt_time'] = time();
    } elseif ($_SESSION['register_attempts'] >= $max_attempts) {
        $registrationMessage = "Too many registration attempts. Please try again later.";
    }
}

// Initialize error variable
$errors = [];

// Process POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token.";
    }
    
    // Initialize registration attempts tracking if not already set
    if (!isset($_SESSION['register_attempts'])) {
        $_SESSION['register_attempts'] = 0;
        $_SESSION['first_attempt_time'] = time();
    }
    
    // Retrieve and sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Username validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = "Username must be 3-20 characters and contain only letters, numbers, and underscores.";
    }
    
    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 12) {
        $errors[] = "Password must be at least 12 characters long.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])(?!.*\s)(?!.*(.)\1{2,})[A-Za-z\d@$!%*?&]{12,}$/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, one number, one special character, no spaces, and no repeating characters more than twice in a row.";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($errors)) {
        try {
            executeInTransaction(function($pdo) use ($username, $email, $password, &$errors) {
                // Check if username exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $errors[] = "Username already exists.";
                }
                
                // Check if email exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $errors[] = "Email already registered.";
                }
                
                if (empty($errors)) {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$username, $email, $password_hash]);
                }
            });
            
            if (empty($errors)) {
                $_SESSION['registration_message'] = "Registration successful";
                // Redirect to the login page or another page after successful registration.
                header("Location: auth/login.php");
                exit;
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = "An error occurred during registration. Please try again.";
        }
    } else {
        $_SESSION['register_attempts']++;
    }
    
    // If there are errors, set the error message and do not redirect so that errors display.
    if (!empty($errors)) {
        $_SESSION['registration_message'] = "Registration unsuccessful: " . implode(" ", $errors);
        $registrationMessage = $_SESSION['registration_message'];
        unset($_SESSION['registration_message']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Student Hub</title>
    <link rel="stylesheet" type="text/css" href="assets/css/auth.css">
    <style>
        /* Style for the notification message */
        #registration-message {
            background-color: #ddffdd;
            color: #006600;
            padding: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
            margin-bottom: 20px;
            border: 1px solid #aaffaa;
        }
        .errors p {
            color: red;
            margin: 0 0 5px;
        }
    </style>
</head>
<!-- Note the additional "registration-page" class on the body tag -->
<body class="registration-page">
    <?php if (!empty($registrationMessage)): ?>
        <div id="registration-message"><?php echo htmlspecialchars($registrationMessage); ?></div>
        <script>
            // Hide the message after 3 seconds
            setTimeout(function(){
                var msgDiv = document.getElementById("registration-message");
                if (msgDiv) {
                    msgDiv.style.display = "none";
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="container">
        <h2>Register</h2>
        
        <!-- Display errors if they exist -->
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $err): ?>
                    <p><?php echo htmlspecialchars($err); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required 
                       pattern="[a-zA-Z0-9_]{3,20}"
                       title="3-20 characters, letters, numbers, and underscores only"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required
                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                       title="Must contain at least 8 characters, one uppercase, one lowercase, one number and one special character">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <button type="submit">Register</button>
        </form>
        <p>
            Already have an account? <a href="auth/login.php">Login here</a>
        </p>
    </div>
</body>
</html>
