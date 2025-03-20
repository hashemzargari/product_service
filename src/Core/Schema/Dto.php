<?php

namespace Hertz\ProductService\Core\Schema;

use ReflectionClass;
use ReflectionProperty;
use ReflectionMethod;


abstract class Dto
{
    private array $data = [];
    private array $errors = [];
    private bool $isValid = true;
    private array $customValidators = [];

    public function __construct()
    {
        $this->registerCustomValidators();
    }

    private function registerCustomValidators(): void
    {
        $reflection = new ReflectionClass($this);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(Validator::class);
            if (empty($attributes)) {
                continue;
            }

            $validator = $attributes[0]->newInstance();
            $this->customValidators[$validator->name] = [
                'validator' => [$this, $method->getName()],
                'message' => $validator->message
            ];
        }
    }

    /**
     * Create a new DTO instance from array data
     */
    public static function fromArray(array $data): static
    {
        $dto = new static();
        $dto->bind($data);
        return $dto;
    }

    /**
     * Create a new DTO instance from JSON string
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        return static::fromArray($data);
    }

    /**
     * Bind data to the DTO
     */
    public function bind(array $data): void
    {
        try {
            $reflection = new ReflectionClass($this);
            $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

            foreach ($properties as $property) {
                $field = $this->getFieldAttribute($property);
                if (!$field)
                    continue;

                $name = $field->getName() ?? $property->getName();
                $value = $data[$name] ?? null;

                if ($field->isRequired() && $value === null) {
                    $this->addError($name, 'Field is required');
                    continue;
                }

                if ($value === null && $field->getDefault() !== null) {
                    $value = $field->getDefault();
                }
                $value = $this->validateAndTransform($value, $field);

                $this->data[$property->getName()] = $value;
            }

            $this->validate();
        } catch (\ReflectionException $e) {
            throw new \RuntimeException('Failed to bind data: ' . $e->getMessage());
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred while binding data: ' . $e->getMessage());
        }
    }

    /**
     * Get field attribute for a property
     */
    private function getFieldAttribute(ReflectionProperty $property): ?Field
    {
        $attributes = $property->getAttributes(Field::class);
        return $attributes[0]?->newInstance();
    }

    /**
     * Validate and transform value based on field type
     */
    private function validateAndTransform(mixed $value, Field $field): mixed
    {
        if ($field->type) {
            $value = match ($field->type) {
                'string' => (string) $value,
                'int', 'integer' => (int) $value,
                'float' => (float) $value,
                'bool', 'boolean' => (bool) $value,
                'array' => (array) $value,
                'datetime' => new \DateTime($value),
                default => $value
            };
        }

        if ($field->validation) {
            $this->validateField($value, $field);
        }

        return $value;
    }

    /**
     * Validate a field value
     */
    private function validateField(mixed $value, Field $field): void
    {
        $validations = explode('|', $field->validation);
        foreach ($validations as $validation) {
            // Check for custom validators first
            if (isset($this->customValidators[$validation])) {
                $validator = $this->customValidators[$validation];
                if (!($validator['validator'])($value)) {
                    $this->addError($field->name, $validator['message']);
                }
                continue;
            }

            // Built-in validators
            match (true) {
                $validation === 'email' => !filter_var($value, FILTER_VALIDATE_EMAIL)
                ? $this->addError($field->name, 'Invalid email format')
                : null,

                $validation === 'url' => !filter_var($value, FILTER_VALIDATE_URL)
                ? $this->addError($field->name, 'Invalid URL format')
                : null,

                $validation === 'numeric' => !is_numeric($value)
                ? $this->addError($field->name, 'Value must be numeric')
                : null,

                $validation === 'alpha' => !ctype_alpha($value)
                ? $this->addError($field->name, 'Value must contain only letters')
                : null,

                $validation === 'alphanum' => !ctype_alnum($value)
                ? $this->addError($field->name, 'Value must contain only letters and numbers')
                : null,

                str_starts_with($validation, 'min:') => strlen($value) < (int) substr($validation, 4)
                ? $this->addError($field->name, "Value must be at least " . substr($validation, 4) . " characters")
                : null,

                str_starts_with($validation, 'max:') => strlen($value) > (int) substr($validation, 4)
                ? $this->addError($field->name, "Value must not exceed " . substr($validation, 4) . " characters")
                : null,

                default => null
            };
        }
    }

    /**
     * Add validation error
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
        $this->isValid = false;
    }

    /**
     * Get all validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if DTO is valid
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Get DTO data as array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Get DTO data as JSON
     */
    public function toJson(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    /**
     * Magic method to get property value
     */
    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic method to set property value
     */
    public function __set(string $name, mixed $value): void
    {
        $reflection = new ReflectionClass($this);
        if ($reflection->hasProperty($name)) {
            $property = $reflection->getProperty($name);
            $field = $this->getFieldAttribute($property);

            if ($field && $field->readOnly) {
                throw new \RuntimeException("Property {$name} is read-only");
            }

            $this->data[$name] = $value;
        } else {
            throw new \RuntimeException("Property {$name} does not exist");
        }
    }

    /**
     * Magic method to check if property exists
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Magic method to unset property
     */
    public function __unset(string $name): void
    {
        unset($this->data[$name]);
    }

    /**
     * Get all field attributes
     */
    public static function getFields(): array
    {
        $reflection = new ReflectionClass(static::class);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $fields = [];

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Field::class);
            if (!empty($attributes)) {
                $field = $attributes[0]->newInstance();
                $fields[$property->getName()] = $field;
            }
        }

        return $fields;
    }

    /**
     * Validate DTO data
     */
    protected function validate(): void
    {
        // Override this method in child classes to add custom validation
    }
}