<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth_check.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemPath = $_POST['item_path'] ?? '';
    if (empty($itemPath)) {
        echo json_encode(['success' => false, 'message' => 'Item path is required.']);
        exit;
    }
    // Define user directory and ensure the target path is within it
    $userDir = realpath(UPLOAD_DIR . "user_{$_SESSION['user_id']}");
    $target = realpath(UPLOAD_DIR . "user_{$_SESSION['user_id']}/" . $itemPath);
    
    if ($target === false || strpos($target, $userDir) !== 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid path.']);
        exit;
    }
    
    if (is_dir($target)) {
        // Recursively delete a folder
        function deleteDir($dir) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? deleteDir("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        if (deleteDir($target)) {
            echo json_encode(['success' => true, 'message' => 'Folder deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete folder.']);
        }
    } elseif (is_file($target)) {
        if (unlink($target)) {
            echo json_encode(['success' => true, 'message' => 'File deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Item does not exist.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
