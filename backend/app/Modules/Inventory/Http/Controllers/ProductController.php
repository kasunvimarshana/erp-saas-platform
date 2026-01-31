<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Core\BaseController;
use App\Modules\Inventory\Services\ProductService;
use App\Modules\Inventory\Requests\CreateProductRequest;
use App\Modules\Inventory\Requests\UpdateProductRequest;
use App\Modules\Inventory\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

/**
 * Product Controller
 * 
 * Handles CRUD operations for product management
 * 
 * @package App\Modules\Inventory\Http\Controllers
 */
class ProductController extends BaseController
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of products
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = $this->service->getAllProducts();
        return $this->collection(ProductResource::collection($products));
    }

    /**
     * Store a newly created product
     *
     * @param CreateProductRequest $request
     * @return JsonResponse
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = $this->service->createProduct($request->validated());
        return $this->created(new ProductResource($product));
    }

    /**
     * Display the specified product
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->service->getProduct($id);
        
        if (!$product) {
            return $this->notFound();
        }
        
        return $this->resource(new ProductResource($product));
    }

    /**
     * Update the specified product
     *
     * @param UpdateProductRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $result = $this->service->updateProduct($id, $request->validated());
        
        if (!$result) {
            return $this->notFound();
        }
        
        return $this->success(null, 'Product updated successfully');
    }

    /**
     * Remove the specified product
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->service->deleteProduct($id);
        
        if (!$result) {
            return $this->notFound();
        }
        
        return $this->noContent();
    }
}
