<?php

namespace Hertz\ProductService\ControllerDtos\Product\RetrieveProduct;

use Hertz\ProductService\Core\Schema\Dto;
use Hertz\ProductService\Core\Schema\Attributes\Field;

class Request extends Dto
{
    #[Field(name: 'id', type: 'integer', required: true)]
    public ?int $id = null;
}
