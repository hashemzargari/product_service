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

    public function __construct(Request $request, ?ProductRepository $repository = null)
    {
        $this->repository = $repository ?? new ProductRepository();
        $this->request = RetrieveProductRequest::fromArray($request->getAll());
        $this->request->isValid(true);
    }

    public function getData(): ?ProductEntity
    {
        if ($this->request->id === null) {
            return null;
        }
        return $this->repository->findById($this->request->id);
    }
}