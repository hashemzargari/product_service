<?php

namespace Hertz\ProductService\Core\Schema\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field
{
    public function __construct(
        public ?string $name = null,
        public ?string $type = null,
        public ?string $validation = null,
        public mixed $default = null,
        public bool $required = false,
        public bool $readOnly = false,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getValidation(): ?string
    {
        return $this->validation;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
}