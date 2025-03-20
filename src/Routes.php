<?php

namespace Hertz\ProductService;

use Hertz\ProductService\Core\Router\Route;
use Hertz\ProductService\Core\Router\BasicRouter;
use Hertz\ProductService\Controller\Init;

class Routes
{
    final public static function registerRoutes(BasicRouter $router): void
    {
        self::registerInitRoute($router);
    }

    final private static function registerInitRoute(BasicRouter $router): void
    {
        $router->addRoute(new Route('/init/', 'GET', Init::class));
    }
}