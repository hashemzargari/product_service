<?php

namespace Hertz\ProductService\Core\Config;

use \PDO;

class DatabaseConfig
{
    public static function getPdo(): PDO
    {
        $dbPath = __DIR__ . '/../../var/db/database.sqlite';
        return new PDO("sqlite:{$dbPath}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
}