<?php

namespace Hertz\ProductService\Core\Logger;

use Hertz\ProductService\Core\Config\LogConfig;

class Logger
{
    private static ?Logger $instance = null;
    private \Monolog\Logger $logger;

    private function __construct()
    {
        $this->logger = LogConfig::getLogger();
    }

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}