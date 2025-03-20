<?php

namespace Hertz\ProductService\Core;

use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Core\Http\Response;
use Hertz\ProductService\Core\Router\BasicRouter;
use Hertz\ProductService\Core\Http\StatusCode;
use Hertz\ProductService\Core\Controller\BaseController;
class App
{
    private BasicRouter $router;

    public function __construct()
    {
        $this->router = new BasicRouter();
    }

    public function getRouter(): BasicRouter
    {
        return $this->router;
    }

    public function run(): void
    {
        $request = new Request();
        $response = $this->handleRequest($request);
        $response->send();
    }

    /**
     * Handle the incoming request and return a response
     * 
     * @param Request $request The incoming request
     * @return Response The response to send back
     */
    private function handleRequest(Request $request): Response
    {
        $route = $this->router->match($request);
        if ($route === null) {
            return new Response('Not Found', StatusCode::NOT_FOUND);
        }

        /** @var BaseController $controller */
        $controller = new ($route->getController());
        return $controller->handleRequest($request);
    }

}