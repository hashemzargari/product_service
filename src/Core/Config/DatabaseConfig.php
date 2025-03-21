<?php

namespace Hertz\ProductService\Core\Config;

use \PDO;
use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;

class DatabaseConfig
{
    private static ?array $config = null;

    public static function loadConfig(string $environment = 'local'): void
    {
        $configFile = __DIR__ . "/../../../configs/{$environment}.yaml";
        if (!file_exists($configFile)) {
            throw new InvalidArgumentException("Config file not found for environment: {$environment}");
        }

        self::$config = Yaml::parseFile($configFile);
    }

    public static function validate(array $config): void
    {
        $required = ['driver', 'host', 'port', 'name', 'user', 'password'];

        if (!isset($config['database'])) {
            throw new InvalidArgumentException('Database configuration is missing');
        }

        foreach ($required as $field) {
            if (!isset($config['database'][$field])) {
                throw new InvalidArgumentException("Required database field missing: {$field}");
            }
        }
    }

    public static function getPdo(): PDO
    {
        if (self::$config === null) {
            self::loadConfig();
        }

        self::validate(self::$config);

        $db = self::$config['database'];
        $dsn = "{$db['driver']}:host={$db['host']};port={$db['port']};dbname={$db['name']}";

        return new PDO($dsn, $db['user'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    public static function getCredentials(): array
    {
        if (self::$config === null) {
            self::loadConfig();
        }

        self::validate(self::$config);

        return self::$config['database'];
    }
}