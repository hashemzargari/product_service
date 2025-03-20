<?php

namespace Hertz\ProductService\View;

use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Core\View\BaseView;
use Hertz\ProductService\Core\Schema\Dto;
use Hertz\ProductService\Repository\ProductRepository;
use Hertz\ProductService\Entity\ProductEntity;
use Hertz\ProductService\ControllerDtos\Product\RetrieveProduct\Request as RetrieveProductRequest;
class ProductsView extends BaseView
{
    protected ProductRepository $repository;
    protected RetrieveProductRequest $request;
    public function __construct(Request $request)
    {
        $this->repository = new ProductRepository();
        // validation handled in Dto automatically
        $this->request = RetrieveProductRequest::fromArray($request->getQueryParams());
    }
    public function getData(): ?ProductEntity
    {
        return $this->repository->findById($this->request->id);
    }
}