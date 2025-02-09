<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPath = $_POST['old_path'] ?? '';
    $newName = $_POST['new_name'] ?? '';
    if (empty($oldPath) || empty($newName)) {
        echo json_encode(['success' => false, 'message' => 'Old path and new name are required.']);
        exit;
    }
    // Sanitize new name (allow letters, numbers, underscores, dashes and dots)
    $newName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $newName);
    $userDir = realpath(UPLOAD_DIR . "user_{$_SESSION['user_id']}");
    $oldFullPath = realpath(UPLOAD_DIR . "user_{$_SESSION['user_id']}/" . $oldPath);
    
    if ($oldFullPath === false || strpos($oldFullPath, $userDir) !== 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid path.']);
        exit;
    }
    
    $newFullPath = dirname($oldFullPath) . DIRECTORY_SEPARATOR . $newName;
    
    if (rename($oldFullPath, $newFullPath)) {
        echo json_encode(['success' => true, 'message' => 'Item renamed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Rename failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
