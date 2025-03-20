<?php

namespace Hertz\ProductService\Core\DB\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ForeignKey
{
    public function __construct(
        public string $entity,
        public ?string $column = null,
        public ?string $onDelete = null,
        public ?string $onUpdate = null,
    ) {
    }
}