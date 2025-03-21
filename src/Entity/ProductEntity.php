<?php

namespace Hertz\ProductService\Entity;

use \DateTime;
use Hertz\ProductService\Core\DB\BaseEntity;
use Hertz\ProductService\Core\DB\Attributes\Id;
use Hertz\ProductService\Core\DB\Attributes\Column;
use Hertz\ProductService\Core\DB\Attributes\Table;


#[Table(name: 'products')]
class ProductEntity extends BaseEntity
{
    #[Column(name: 'name', type: 'string', nullable: false)]
    public string $name;

    #[Column(name: 'description', type: 'string', nullable: false)]
    public string $description;

    #[Column(name: 'price', type: 'float', nullable: false)]
    public float $price;

    #[Column(name: 'category', type: 'string', nullable: false)]
    public string $category;

    // todo: for attributes fields, we need to add a new attribute to the entity class (like @ManyToOne, @OneToMany, @ManyToMany, etc.)
}