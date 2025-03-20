<?php

namespace Hertz\ProductService\Repository;

use Hertz\ProductService\Core\Config\DatabaseConfig;
use \Pdo;
use Hertz\ProductService\Core\DB\BaseRepository;
use Hertz\ProductService\Entity\ProductEntity;

class ProductRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(DatabaseConfig::getPdo(), ProductEntity::class);
    }

}