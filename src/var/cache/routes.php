<?php return array (
  'GET ' => 
  array (
    'products' => 
    array (
      'params' => 
      array (
        0 => 'id',
      ),
      '*' => 
      array (
        'route' => 
        \Hertz\ProductService\Core\Routing\Route::__set_state(array(
           'path' => '/products/{id}',
           'method' => 'GET',
           'controller' => 'Hertz\\ProductService\\Controller\\Product\\RetrieveProductController',
           'isStatic' => false,
           'params' => 
          array (
          ),
        )),
      ),
    ),
  ),
);