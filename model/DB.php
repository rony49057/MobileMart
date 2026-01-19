<?php
require_once __DIR__ . '/../config.php';

class DB {
    private static $pdo = null;

    public static function conn() {
        if (self::$pdo !== null) return self::$pdo;

        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (Exception $e) {
            // Simple error output for development
            die('Database connection failed: ' . e($e->getMessage()));
        }
        return self::$pdo;
    }
}


