<?php

namespace Hertz\ProductService\Core;

use Hertz\ProductService\Core\Config\DatabaseConfig;
use Hertz\ProductService\Core\Config\LogConfig;
use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Core\Http\Response;
use Hertz\ProductService\Core\Routing\BasicRouter;
use Hertz\ProductService\Core\Http\StatusCode;
use Hertz\ProductService\Core\Controller\BaseController;
use Hertz\ProductService\Core\Logger\Logger;

class App
{
    private BasicRouter $router;
    private Logger $logger;

    public function __construct()
    {
        // Get environment from env var or default to local
        $environment = getenv('APP_ENV') ?: 'local';

        // Initialize all configs
        $this->initializeConfigs($environment);

        $this->router = new BasicRouter();
        $this->logger = Logger::getInstance();
        $this->logger->info('Application initialized', ['environment' => $environment]);
    }

    /**
     * Initialize all configuration classes
     */
    private function initializeConfigs(string $environment): void
    {
        // Load configs from Config namespace
        DatabaseConfig::loadConfig($environment);
        LogConfig::loadConfig($environment);
    }

    public function getRouter(): BasicRouter
    {
        return $this->router;
    }

    public function run(): void
    {
        $this->logger->info('Starting application');
        $request = new Request();
        $response = $this->handleRequest($request);
        $response->send();
        $this->logger->info('Application finished processing request');
    }

    /**
     * Handle the incoming request and return a response
     * 
     * @param Request $request The incoming request
     * @return Response The response to send back
     */
    private function handleRequest(Request $request): Response
    {
        $this->logger->debug('Processing request', [
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'query' => $request->getQueryParams(),
            'body' => $request->getRequestBody()
        ]);

        $route = $this->router->match($request);

        if ($route === null) {
            $this->logger->warning('Route not found', ['path' => $request->getPath()]);
            return new Response('Not Found', StatusCode::NOT_FOUND);
        }

        $this->logger->info('Route matched', [
            'controller' => $route->getController(),
            'path' => $request->getPath()
        ]);

        // Set path parameters from the matched route
        $request->setPathParams($route->getParams());

        /** @var BaseController $controller */
        $controller = new ($route->getController());
        return $controller->handleRequest($request);
    }
}