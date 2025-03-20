<?php

namespace Hertz\ProductService\Controller;

use Hertz\ProductService\Core\Controller\BaseController;
use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Core\Http\Response;
class Init extends BaseController
{
    public function indexAction(Request $request): Response
    {
        return new Response('Hello World!');
    }

    public function testAction(Request $request): Response
    {
        return new Response('Test');
    }
}
