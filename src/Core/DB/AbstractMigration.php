<?php

namespace Hertz\ProductService\Core\DB;

use Phinx\Migration\AbstractMigration as PhinxMigration;
use Hertz\ProductService\Core\DB\BaseEntity;

abstract class AbstractMigration extends PhinxMigration
{
    /**
     * Create a table based on an Entity class
     */
    protected function createEntityTable(string $entityClass): void
    {
        /**
         * @var BaseEntity $entity
         */
        $entity = new $entityClass();
        $tableName = $entity->getTable();
        $columns = $entity->getColumns();
        $primaryKey = $entity->getPrimaryKey();
        $foreignKeys = $entity->getForeignKeys();
        $indexes = $entity->getIndexes();

        $table = $this->table($tableName);

        // Add columns
        foreach ($columns as $propertyName => $column) {
            $options = [
                'null' => $column['nullable'] ?? false,
                'default' => $column['default'] ?? null,
                'limit' => $column['length'] ?? null,
                'precision' => $column['precision'] ?? null,
                'scale' => $column['scale'] ?? null,
                'comment' => $column['comment'] ?? null,
            ];

            // Remove null values from options
            $options = array_filter($options, fn($value) => $value !== null);

            $table->addColumn($column['name'], $column['type'], $options);
        }

        // Add primary key
        if (!empty($primaryKey)) {
            $table->addIndex([$primaryKey['property']], ['unique' => true]);
        }

        // Add foreign keys
        foreach ($foreignKeys as $propertyName => $fk) {
            $table->addForeignKey(
                $fk['column'],
                $fk['entity']::getTable(),
                'id',
                [
                    'delete' => $fk['onDelete'] ?? 'CASCADE',
                    'update' => $fk['onUpdate'] ?? 'CASCADE',
                ]
            );
        }

        // Add indexes
        foreach ($indexes as $indexName => $index) {
            $table->addIndex($index['columns'], [
                'unique' => $index['unique'] ?? false,
                'name' => $indexName,
            ]);
        }

        $table->create();
    }

    /**
     * Drop a table based on an Entity class
     */
    protected function dropEntityTable(string $entityClass): void
    {
        /**
         * @var Entity $entity
         */
        $entity = new $entityClass();
        $this->table($entity->getTable())->drop();
    }
}