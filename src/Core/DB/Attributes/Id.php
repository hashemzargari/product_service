<?php

namespace Hertz\ProductService\Core\DB\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Id
{
    public function __construct(
        public bool $autoIncrement = true,
        public ?string $generator = null,
    ) {
    }
}