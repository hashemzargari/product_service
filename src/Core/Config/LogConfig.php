<?php

namespace Hertz\ProductService\Core\Config;

use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class LogConfig
{
    private static ?array $config = null;
    private static ?Logger $logger = null;

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
        $required = ['level', 'path'];

        if (!isset($config['log'])) {
            throw new InvalidArgumentException('Log configuration is missing');
        }

        foreach ($required as $field) {
            if (!isset($config['log'][$field])) {
                throw new InvalidArgumentException("Required log field missing: {$field}");
            }
        }
    }

    public static function getLogger(): Logger
    {
        if (self::$logger !== null) {
            return self::$logger;
        }

        if (self::$config === null) {
            self::loadConfig();
        }

        self::validate(self::$config);

        $log = self::$config['log'];
        $level = strtoupper($log['level']);

        self::$logger = new Logger('product_service');
        self::$logger->pushHandler(
            new StreamHandler(
                $log['path'] . '/app.log',
                Level::fromName($level)
            )
        );

        return self::$logger;
    }
}