<?php
namespace Model;

use PDO;

class Connection
{
    private static $stmt;

    public static function getInstance(): PDO
    {
        if (empty(self::$stmt)) {
            self::$stmt = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return self::$stmt;
    }
}
