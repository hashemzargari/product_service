<?php

namespace Hertz\ProductService\Core\DB;

use Hertz\ProductService\Core\DB\BaseEntity;
use Phinx\Migration\AbstractMigration;
use ReflectionClass;
use ReflectionProperty;

class MigrationGenerator
{
    private const TYPE_MAPPING = [
        'int' => 'integer',
        'string' => 'string',
        'float' => 'float',
        'bool' => 'boolean',
        'array' => 'json',
        'DateTime' => 'datetime',
        'DateTimeImmutable' => 'datetime',
        '?int' => 'integer',
        '?string' => 'string',
        '?float' => 'float',
        '?bool' => 'boolean',
        '?array' => 'json',
        '?DateTime' => 'datetime',
        '?DateTimeImmutable' => 'datetime',
    ];

    public static function generateMigration(string $entityClass): string
    {
        if (!class_exists($entityClass)) {
            throw new \InvalidArgumentException("Class {$entityClass} does not exist");
        }

        if (!is_subclass_of($entityClass, BaseEntity::class)) {
            $parentClass = get_parent_class($entityClass);
            throw new \InvalidArgumentException("Class {$entityClass} must extend BaseEntity. Current parent class: " . ($parentClass ?: 'none'));
        }

        $reflection = new ReflectionClass($entityClass);
        $tableName = self::getTableName($reflection);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        // Get parent class properties to skip them
        $parentProperties = [];
        $parentClass = $reflection->getParentClass();
        if ($parentClass) {
            $parentProperties = array_map(
                fn($prop) => $prop->getName(),
                $parentClass->getProperties(ReflectionProperty::IS_PUBLIC)
            );
        }

        $migrationContent = "<?php\n\n";
        $migrationContent .= "use Phinx\\Migration\\AbstractMigration;\n\n";
        $migrationContent .= "class Create" . $reflection->getShortName() . "Table extends AbstractMigration\n";
        $migrationContent .= "{\n";
        $migrationContent .= "    public function change(): void\n";
        $migrationContent .= "    {\n";
        $migrationContent .= "        \$table = \$this->table('{$tableName}');\n\n";

        foreach ($properties as $property) {
            // Skip properties from parent class
            if (in_array($property->getName(), $parentProperties)) {
                continue;
            }

            $type = $property->getType();
            if (!$type)
                continue;

            $typeName = $type->getName();
            $isNullable = $type->allowsNull();
            $columnOptions = [];

            // Map PHP types to database types
            $dbType = self::TYPE_MAPPING[$typeName] ?? 'string';

            // Handle special cases
            if ($typeName === 'string') {
                $columnOptions['limit'] = 255;
            } elseif ($typeName === 'float' || $typeName === '?float') {
                $dbType = 'decimal';
                $columnOptions['precision'] = 10;
                $columnOptions['scale'] = 2;
            }

            // Add nullable option if needed
            if ($isNullable) {
                $columnOptions['null'] = true;
            }

            // Add unique constraint if property has #[Unique] attribute
            if (self::hasAttribute($property, 'Unique')) {
                $columnOptions['unique'] = true;
            }

            // Build column options string
            $optionsString = empty($columnOptions) ? '' : ', ' . var_export($columnOptions, true);

            $migrationContent .= "        \$table->addColumn('{$property->getName()}', '{$dbType}'{$optionsString});\n";
        }

        // Add timestamps
        $migrationContent .= "\n        \$table->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP']);\n";
        $migrationContent .= "        \$table->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP']);\n\n";

        $migrationContent .= "        \$table->create();\n";
        $migrationContent .= "    }\n";
        $migrationContent .= "}\n";

        return $migrationContent;
    }

    private static function getTableName(ReflectionClass $reflection): string
    {
        // Check for Table attribute first
        $tableAttributes = $reflection->getAttributes('Hertz\\ProductService\\Core\\DB\\Attributes\\Table');
        if (!empty($tableAttributes)) {
            $table = $tableAttributes[0]->newInstance();
            if ($table->name !== null) {
                return $table->name;
            }
        }

        // Fallback to converting class name to snake_case
        $className = $reflection->getShortName();
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }

    private static function hasAttribute(ReflectionProperty $property, string $attributeName): bool
    {
        $attributes = $property->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === $attributeName) {
                return true;
            }
        }
        return false;
    }
}