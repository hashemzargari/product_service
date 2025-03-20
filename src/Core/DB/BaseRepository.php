<?php

namespace Hertz\ProductService\Core\DB;

use Hertz\ProductService\Core\Schema\Field;
use Hertz\ProductService\Core\DB\Attributes\Table;
use PDO;
use ReflectionClass;
use ReflectionProperty;

abstract class BaseRepository implements RepositoryInterface
{
    protected PDO $db;
    protected string $entityClass;

    public function __construct(PDO $db, string $entityClass)
    {
        $this->db = $db;
        $this->entityClass = $entityClass;
    }

    protected function getTableName(): string
    {
        $reflection = new ReflectionClass($this->entityClass);
        $tableAttribute = $reflection->getAttributes(Table::class)[0] ?? null;
        return $tableAttribute ? $tableAttribute->newInstance()->name : strtolower($reflection->getShortName());
    }

    protected function getFields(): array
    {
        $reflection = new ReflectionClass($this->entityClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $fields = [];

        foreach ($properties as $property) {
            $fieldAttribute = $property->getAttributes(Field::class)[0] ?? null;
            if ($fieldAttribute) {
                $field = $fieldAttribute->newInstance();
                $fields[$property->getName()] = $field;
            }
        }

        return $fields;
    }

    protected function mapToEntity(array $data): Entity
    {
        $entity = new $this->entityClass();
        $fields = $this->getFields();

        foreach ($fields as $propertyName => $field) {
            $dbName = $field->getName() ?? $propertyName;
            if (isset($data[$dbName])) {
                $value = $this->transformValue($data[$dbName], $field);
                $entity->$propertyName = $value;
            }
        }

        return $entity;
    }

    protected function mapToArray(object $entity): array
    {
        $data = [];
        $fields = $this->getFields();

        foreach ($fields as $propertyName => $field) {
            if (isset($entity->$propertyName)) {
                $dbName = $field->getName() ?? $propertyName;
                $data[$dbName] = $entity->$propertyName;
            }
        }

        return $data;
    }

    protected function transformValue(mixed $value, Field $field): mixed
    {
        if ($field->type) {
            return match ($field->type) {
                'string' => (string) $value,
                'int', 'integer' => (int) $value,
                'float' => (float) $value,
                'bool', 'boolean' => (bool) $value,
                'array' => (array) $value,
                'datetime' => new \DateTime($value),
                default => $value
            };
        }

        return $value;
    }

    public function findAll(): array
    {
        $table = $this->getTableName();
        $stmt = $this->db->prepare("SELECT * FROM {$table}");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => $this->mapToEntity($data), $results);
    }

    public function findById(int $id): ?Entity
    {
        $table = $this->getTableName();
        // var_dump("SELECT * FROM {$table} WHERE id = :id");
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToEntity($data) : null;
    }

    public function create(object $entity): int
    {
        $table = $this->getTableName();
        $data = $this->mapToArray($entity);

        $keys = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO {$table} ({$keys}) VALUES ({$values})");
        $stmt->execute($data);

        return $this->db->lastInsertId();
    }

    public function update(int $id, object $entity): int
    {
        $table = $this->getTableName();
        $data = $this->mapToArray($entity);

        $set = implode(', ', array_map(fn($key) => "{$key} = :{$key}", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE {$table} SET {$set} WHERE id = :id");
        $data['id'] = $id;
        $stmt->execute($data);

        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $table = $this->getTableName();
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount();
    }
}
