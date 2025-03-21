<?php

namespace Hertz\ProductService\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use Hertz\ProductService\View\ProductsView;
use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Entity\ProductEntity;
use Hertz\ProductService\Repository\ProductRepository;
use Mockery;
use Hertz\ProductService\Core\Http\RetrieveProductRequest;

class ProductsViewTest extends TestCase
{
    private $mockRepository;
    private $mockRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = Mockery::mock(ProductRepository::class);
        $this->mockRequest = Mockery::mock(Request::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetDataReturnsProductWhenFound()
    {
        // Arrange
        $productId = 1;
        $expectedProduct = new ProductEntity();
        $expectedProduct->id = $productId;
        $expectedProduct->name = 'Test Product';

        $this->mockRequest->shouldReceive('getAll')
            ->once()
            ->andReturn(['id' => $productId]);

        $this->mockRepository->shouldReceive('findById')
            ->once()
            ->with($productId)
            ->andReturn($expectedProduct);

        $view = new ProductsView($this->mockRequest, $this->mockRepository);

        // Act
        $result = $view->getData();

        // Assert
        $this->assertInstanceOf(ProductEntity::class, $result);
        $this->assertEquals($productId, $result->id);
        $this->assertEquals('Test Product', $result->name);
    }

    public function testGetDataReturnsNullWhenProductNotFound()
    {
        // Arrange
        $productId = 999;

        $this->mockRequest->shouldReceive('getAll')
            ->once()
            ->andReturn(['id' => $productId]);

        $this->mockRepository->shouldReceive('findById')
            ->once()
            ->with($productId)
            ->andReturn(null);

        $view = new ProductsView($this->mockRequest, $this->mockRepository);

        // Act
        $result = $view->getData();

        // Assert
        $this->assertNull($result);
    }

    public function testGetDataReturnsNullWhenIdIsNotProvided()
    {
        // Arrange
        $this->mockRequest->shouldReceive('getAll')
            ->once()
            ->andReturn([]);

        $view = new ProductsView($this->mockRequest, $this->mockRepository);

        // Act
        $result = $view->getData();

        // Assert
        $this->assertNull($result);
    }

    public function __construct(Request $request)
    {
        $this->repository = $this->mockRepository;
        $this->request = RetrieveProductRequest::fromArray($request->getAll());
        $this->request->isValid(true);
    }
}