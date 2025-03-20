<?php

namespace Hertz\ProductService\Tests\Integration\View;

use PHPUnit\Framework\TestCase;
use Hertz\ProductService\View\ProductsView;
use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Repository\ProductRepository;
use Hertz\ProductService\Entity\ProductEntity;

class ProductsViewIntegrationTest extends TestCase
{
    private $repository;
    private $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository();
        $this->request = new Request();
    }

    public function testGetDataReturnsProductFromDatabase()
    {
        // Arrange
        $productId = 1;
        $this->request->setQueryParams(['id' => $productId]);
        $view = new ProductsView($this->request);

        // Act
        $result = $view->getData();

        // Assert
        $this->assertInstanceOf(ProductEntity::class, $result);
        $this->assertEquals($productId, $result->id);
    }
}