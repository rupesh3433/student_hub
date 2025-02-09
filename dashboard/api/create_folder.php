<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderName = trim($_POST['folder_name'] ?? '');
    if (empty($folderName)) {
        echo json_encode(['success' => false, 'message' => 'Folder name is required.']);
        exit;
    }
    // Sanitize folder name: allow only alphanumeric, underscore, and dash
    $folderName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $folderName);
    $userFolderPath = UPLOAD_DIR . "user_{$_SESSION['user_id']}/" . $folderName;
    
    if (!is_dir($userFolderPath)) {
        if (mkdir($userFolderPath, 0777, true)) {
            echo json_encode(['success' => true, 'message' => 'Folder created successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create folder.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Folder already exists.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
