<?php
require_once 'db_pool.php';

function getDBConnection() {
    return DatabasePool::getInstance();
}

// For transactions and error handling
function executeInTransaction($callback) {
    $pdo = getDBConnection();
    try {
        $pdo->beginTransaction();
        $result = $callback($pdo);
        $pdo->commit();
        return $result;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Database transaction failed: " . $e->getMessage());
        throw $e;
    }
} 