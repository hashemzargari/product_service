<?php

namespace Hertz\ProductService\Core\DB\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(
        public ?string $name = null,
        public ?string $schema = null,
        public array $indexes = [],
        public array $uniqueConstraints = [],
    ) {
    }
}