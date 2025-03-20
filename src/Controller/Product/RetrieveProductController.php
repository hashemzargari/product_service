<?php

namespace Hertz\ProductService\Controller\Product;

use Hertz\ProductService\Core\Controller\BaseController;
use Hertz\ProductService\View\ProductsView;
use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Core\Http\Response;
use Hertz\ProductService\Core\Http\StatusCode;
use Hertz\ProductService\ControllerDtos\Product\RetrieveProduct\Response as ProductRetrieveResponse;

class RetrieveProductController extends BaseController
{
    public function indexAction(Request $request): ProductRetrieveResponse|Response
    {
        $product = (new ProductsView($request))->getData();
        if ($product === null) {
            return new Response('Product not found', StatusCode::NOT_FOUND);
        }
        return ProductRetrieveResponse::fromArray([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category,
            'price' => $product->price,
            'createdAt' => $product->createdAt,
            'updatedAt' => $product->updatedAt,
        ]);
    }
}