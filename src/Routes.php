<?php

namespace Hertz\ProductService;

use Hertz\ProductService\Core\Routing\Route;
use Hertz\ProductService\Core\Routing\BasicRouter;
use Hertz\ProductService\Controller\Init;
use Hertz\ProductService\Controller\Product\RetrieveProductController;

class Routes
{
    final public static function registerRoutes(BasicRouter $router): void
    {
        self::registerInitRoute($router);
        self::registerProductRoutes($router);
    }

    final private static function registerInitRoute(BasicRouter $router): void
    {
        $router->addRoute(new Route('/init/', 'GET', Init::class));
    }

    final private static function registerProductRoutes(BasicRouter $router): void
    {
        $router->addRoute(new Route('/products/{id}', 'GET', RetrieveProductController::class));
    }
}