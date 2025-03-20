<?php

require_once __DIR__ . '/../vendor/autoload.php';

// create the app
$app = new Hertz\ProductService\Core\App();

// register the routes
Hertz\ProductService\Routes::registerRoutes($app->getRouter());

// run the app
$app->run();
