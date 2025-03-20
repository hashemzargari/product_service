<?php

namespace Hertz\ProductService\Core\Schema;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field {
    public function __construct(
        public ?string $type = null,
        public ?string $name = null,
        public bool $required = false,
        public mixed $default = null,
        public ?string $validation = null,
        public bool $readOnly = false,
        public bool $writeOnly = false,
        public ?string $description = null
    ) {}

    public function getType(): ?string {
        return $this->type;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function isRequired(): bool {
        return $this->required;
    }

    public function getDefault(): mixed {
        return $this->default;
    }

    public function getValidation(): ?string {
        return $this->validation;
    }

    public function isReadOnly(): bool {
        return $this->readOnly;
    }

    public function isWriteOnly(): bool {
        return $this->writeOnly;
    }

    public function getDescription(): ?string {
        return $this->description;
    }
}