<?php

namespace Hertz\ProductService\ControllerDtos\Product\RetrieveProduct;

use Hertz\ProductService\Core\Schema\Dto;
use Hertz\ProductService\Core\Schema\Field;

class Request extends Dto
{
    #[Field(type: 'integer', required: true)]
    public int $id;
}
