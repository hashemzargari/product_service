<?php

namespace Hertz\ProductService\Core\DB\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Index
{
    public function __construct(
        public string $name,
        public array $columns = [],
        public bool $unique = false,
    ) {
    }
}