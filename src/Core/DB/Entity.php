<?php

namespace Hertz\ProductService\Core\DB;

use Hertz\ProductService\Core\Schema\Dto;
use Hertz\ProductService\Core\DB\Attributes\Column;
use Hertz\ProductService\Core\DB\Attributes\Id;
use Hertz\ProductService\Core\DB\Attributes\ForeignKey;
use Hertz\ProductService\Core\DB\Attributes\Index;
use Hertz\ProductService\Core\DB\Attributes\Table;
use ReflectionClass;
use ReflectionProperty;

abstract class Entity extends Dto
{
    #[Id]
    #[Column(type: 'int', nullable: false)]
    public int $id;

    /**
     * Get the table name for this entity
     */
    public function getTable(): string
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes(Table::class);

        if (empty($attributes)) {
            // If no Table attribute is defined, use the class name in snake_case
            return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $reflection->getShortName()));
        }

        $table = $attributes[0]->newInstance();
        return $table->name ?? strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $reflection->getShortName()));
    }

    /**
     * Get all column definitions for this entity
     */
    public function getColumns(): array
    {
        $columns = [];
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $columnAttributes = $property->getAttributes(Column::class);
            if (empty($columnAttributes)) {
                continue;
            }

            $column = $columnAttributes[0]->newInstance();
            $columns[$property->getName()] = [
                'name' => $column->name ?? $property->getName(),
                'type' => $column->type ?? $this->getPropertyType($property),
                'nullable' => $column->nullable,
                'default' => $column->default,
                'unique' => $column->unique,
                'length' => $column->length,
                'precision' => $column->precision,
                'scale' => $column->scale,
                'comment' => $column->comment,
            ];
        }

        return $columns;
    }

    /**
     * Get primary key configuration
     */
    public function getPrimaryKey(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $idAttributes = $property->getAttributes(Id::class);
            if (!empty($idAttributes)) {
                $id = $idAttributes[0]->newInstance();
                return [
                    'property' => $property->getName(),
                    'autoIncrement' => $id->autoIncrement,
                    'generator' => $id->generator,
                ];
            }
        }

        return [];
    }

    /**
     * Get foreign key relationships
     */
    public function getForeignKeys(): array
    {
        $foreignKeys = [];
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $fkAttributes = $property->getAttributes(ForeignKey::class);
            if (!empty($fkAttributes)) {
                $fk = $fkAttributes[0]->newInstance();
                $foreignKeys[$property->getName()] = [
                    'entity' => $fk->entity,
                    'column' => $fk->column,
                    'onDelete' => $fk->onDelete,
                    'onUpdate' => $fk->onUpdate,
                ];
            }
        }

        return $foreignKeys;
    }

    /**
     * Get indexes
     */
    public function getIndexes(): array
    {
        $indexes = [];
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $indexAttributes = $property->getAttributes(Index::class);
            if (!empty($indexAttributes)) {
                foreach ($indexAttributes as $indexAttribute) {
                    $index = $indexAttribute->newInstance();
                    $indexes[$index->name] = [
                        'columns' => $index->columns,
                        'unique' => $index->unique,
                    ];
                }
            }
        }

        return $indexes;
    }

    /**
     * Get the PHP type of a property
     */
    private function getPropertyType(ReflectionProperty $property): string
    {
        if ($property->hasType()) {
            return $property->getType()->getName();
        }

        // Default to string if no type is specified
        return 'string';
    }
}