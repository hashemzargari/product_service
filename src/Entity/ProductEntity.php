<?php

namespace Hertz\ProductService\Entity;

use \DateTime;
use Hertz\ProductService\Core\DB\Entity;
use Hertz\ProductService\Core\DB\Attributes\Id;
use Hertz\ProductService\Core\DB\Attributes\Column;
use Hertz\ProductService\Core\DB\Attributes\Table;


#[Table(name: 'products')]
class ProductEntity extends Entity
{
    #[Id]
    #[Column(name: 'id', type: 'int', nullable: false)]
    public int $id;

    #[Column(name: 'name', type: 'string', nullable: false)]
    public string $name;

    #[Column(name: 'description', type: 'string', nullable: false)]
    public string $description;

    #[Column(name: 'price', type: 'float', nullable: false)]
    public float $price;

    #[Column(name: 'category', type: 'string', nullable: false)]
    public string $category;

    #[Column(name: 'created_at', type: 'datetime', nullable: false)]
    public DateTime $createdAt;

    #[Column(name: 'updated_at', type: 'datetime', nullable: false)]
    public DateTime $updatedAt;

    // todo: for attributes fields, we need to add a new attribute to the entity class (like @ManyToOne, @OneToMany, @ManyToMany, etc.)
}