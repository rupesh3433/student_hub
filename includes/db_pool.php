<?php
class DatabasePool {
    private static $instances = [];
    private static $maxConnections = 10;
    
    public static function getInstance() {
        $pid = getmypid();
        
        if (!isset(self::$instances[$pid]) || count(self::$instances[$pid]) < self::$maxConnections) {
            try {
                $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
                $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => true
                ]);
                self::$instances[$pid][] = $pdo;
                return $pdo;
            } catch (\PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new \Exception("Database connection failed");
            }
        }
        
        return self::$instances[$pid][array_rand(self::$instances[$pid])];
    }
}