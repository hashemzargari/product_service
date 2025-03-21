<?php

use Phinx\Migration\AbstractMigration;

class CreateProductEntityTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('products');

        $table->addColumn('name', 'string', array (
  'limit' => 255,
));
        $table->addColumn('description', 'string', array (
  'limit' => 255,
));
        $table->addColumn('price', 'decimal', array (
  'precision' => 10,
  'scale' => 2,
));
        $table->addColumn('category', 'string', array (
  'limit' => 255,
));

        $table->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP']);

        $table->create();
    }
}
