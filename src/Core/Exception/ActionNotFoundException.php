<?php

namespace Hertz\ProductService\Core\Exception;

class ActionNotFoundException extends \Exception
{
    public function __construct(string $action, int $code = 0, \Throwable $previous = null)
    {
        $message = "Action {$action} not found";
        parent::__construct($message, $code, $previous);
    }
}