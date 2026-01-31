<?php

namespace App\Modules\Inventory\Repositories;

use App\Core\BaseRepository;
use App\Modules\Inventory\Models\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name): ?Product
    {
        return $this->findBy('name', $name);
    }

    public function getByTenant(int $tenantId)
    {
        return $this->model->where('tenant_id', $tenantId)->get();
    }

    public function getByCategory(string $category)
    {
        return $this->model->where('category', $category)->get();
    }

    public function getByStatus(string $status)
    {
        return $this->model->where('status', $status)->get();
    }

    public function getByBrand(string $brand)
    {
        return $this->model->where('brand', $brand)->get();
    }
}
