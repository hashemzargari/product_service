<?php

namespace Hertz\ProductService\Core\Schema;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Validator
{
    public function __construct(
        public string $name,
        public string $message
    ) {
    }
}