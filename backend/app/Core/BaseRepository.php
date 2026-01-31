<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface BaseRepositoryInterface
 * 
 * @package App\Core
 */
interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
    public function find(int $id, array $columns = ['*']): ?Model;
    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model;
    public function findWhere(array $criteria, array $columns = ['*']): Collection;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function with(array $relations): self;
}

/**
 * Class BaseRepository
 * 
 * Base repository implementation providing common data access patterns
 * 
 * @package App\Core
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @var array
     */
    protected array $with = [];

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->with($this->with)->get($columns);
    }

    /**
     * Paginate records
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->with($this->with)->paginate($perPage, $columns);
    }

    /**
     * Find record by ID
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->with($this->with)->find($id, $columns);
    }

    /**
     * Find record by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Model|null
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model
    {
        return $this->model->with($this->with)->where($field, '=', $value)->first($columns);
    }

    /**
     * Find records by criteria
     *
     * @param array $criteria
     * @param array $columns
     * @return Collection
     */
    public function findWhere(array $criteria, array $columns = ['*']): Collection
    {
        $query = $this->model->with($this->with);
        
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                [$operator, $val] = $value;
                $query->where($field, $operator, $val);
            } else {
                $query->where($field, '=', $value);
            }
        }
        
        return $query->get($columns);
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        
        if (!$record) {
            return false;
        }
        
        return $record->update($data);
    }

    /**
     * Delete record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);
        
        if (!$record) {
            return false;
        }
        
        return $record->delete();
    }

    /**
     * Set relationships to eager load
     *
     * @param array $relations
     * @return self
     */
    public function with(array $relations): self
    {
        $this->with = $relations;
        return $this;
    }

    /**
     * Reset the repository state
     *
     * @return void
     */
    protected function reset(): void
    {
        $this->with = [];
    }
}
