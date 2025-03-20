<?php

namespace Hertz\ProductService\Core\DB;

interface RepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?object;
    public function create(object $entity): int;
    public function update(int $id, object $entity): int;
    public function delete(int $id): int;
}