<?php

namespace Hertz\ProductService\Core\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Validator
{
    public function __construct(
        public string $name,
        public string $message,
    ) {
    }
}