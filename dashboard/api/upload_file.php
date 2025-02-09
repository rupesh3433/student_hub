<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $userDir = UPLOAD_DIR . "user_{$_SESSION['user_id']}/";
        if (!is_dir($userDir)) {
            mkdir($userDir, 0777, true);
        }
        $filename = basename($_FILES['file']['name']);
        $target = $userDir . $filename;

        // Validate file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']; // Add allowed types as needed
        if (!in_array($_FILES['file']['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            exit;
        }

        if ($_FILES['file']['size'] > MAX_UPLOAD_SIZE) {
            echo json_encode(['success' => false, 'message' => 'File exceeds maximum upload size.']);
            exit;
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully.', 'filename' => $filename]);
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload failed.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
