<?php

namespace Hertz\ProductService\Core\DB\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public ?string $name = null,
        public ?string $type = null,
        public bool $nullable = false,
        public mixed $default = null,
        public bool $unique = false,
        public ?int $length = null,
        public ?int $precision = null,
        public ?int $scale = null,
        public ?string $comment = null,
    ) {
    }
}