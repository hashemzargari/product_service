<?php

namespace Hertz\ProductService\Core\View;

use Hertz\ProductService\Core\Schema\Dto;

abstract class BaseView
{
    abstract public function getData(): ?Dto;
}