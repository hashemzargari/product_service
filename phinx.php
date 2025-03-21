<?php

use Hertz\ProductService\Core\Config\DatabaseConfig;

DatabaseConfig::loadConfig();
$config = DatabaseConfig::getCredentials();
$db = [
    'host' => $config['host'] ?: 'db',
    'database' => $config['database'] ?: 'product_service',
    'username' => $config['username'] ?: 'product_service',
    'password' => $config['password'] ?: 'root',
    'port' => $config['port'] ?: '5432'
];

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'pgsql',
            'host' => $db['host'],
            'name' => $db['database'],
            'user' => $db['username'],
            'pass' => $db['password'],
            'port' => $db['port'],
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'pgsql',
            'host' => $db['host'],
            'name' => $db['database'] . '_test',
            'user' => $db['username'],
            'pass' => $db['password'],
            'port' => $db['port'],
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];