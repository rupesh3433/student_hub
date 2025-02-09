<?php
$host = 'localhost';
$db   = 'student_hub';
$user = 'root';
$password = '';
$charset = 'utf8mb4';

// Specify port 3306 in the DSN (optional, since 3306 is MySQL's default)
$dsn = "mysql:host=$host;dbname=$db;port=3306;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
