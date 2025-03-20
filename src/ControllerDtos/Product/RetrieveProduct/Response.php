<?php

namespace Hertz\ProductService\ControllerDtos\Product\RetrieveProduct;

use DateTime;
use Hertz\ProductService\Core\Schema\Dto;
use Hertz\ProductService\Core\Schema\Field;

class Response extends Dto
{
    #[Field(type: 'integer')]
    public int $id;

    #[Field(type: 'string')]
    public string $name;

    #[Field(type: 'string')]
    public string $description;

    #[Field(type: 'string')]
    public string $category;

    #[Field(type: 'float')]
    public float $price;

    #[Field(type: 'datetime')]
    public DateTime $createdAt;

    #[Field(type: 'datetime')]
    public DateTime $updatedAt;
}
