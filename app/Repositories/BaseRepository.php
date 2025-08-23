<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;

abstract class BaseRepository
{
    protected $model;
    protected $cacheService;
    protected $cacheEnabled = true;
    protected $cacheDuration = 60; // minutes

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->cacheService = new CacheService();
    }

    /**
     * Get all records with optional eager loading
     */
    public function all(array $with = [], array $columns = ['*'])
    {
        $query = $this->model->with($with)->select($columns);
        
        if ($this->cacheEnabled) {
            $cacheKey = $this->cacheService->generateKey(
                $this->model->getTable() . '_all',
                $with,
                $columns
            );
            
            return $this->cacheService->remember($cacheKey, function () use ($query) {
                return $query->get();
            }, $this->cacheDuration);
        }
        
        return $query->get();
    }

    /**
     * Get paginated records with filters
     */
    public function paginate(
        int $perPage = 15,
        array $filters = [],
        array $with = [],
        array $columns = ['*'],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc'
    ) {
        $query = $this->model->with($with)->select($columns);
        
        // Apply filters
        $query = $this->applyFilters($query, $filters);
        
        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);
        
        if ($this->cacheEnabled) {
            $cacheKey = $this->cacheService->generateKey(
                $this->model->getTable() . '_paginate',
                $perPage,
                $filters,
                $with,
                $columns,
                $sortBy,
                $sortOrder
            );
            
            return $this->cacheService->remember($cacheKey, function () use ($query, $perPage) {
                return $query->paginate($perPage);
            }, $this->cacheDuration);
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Find record by ID with optional eager loading
     */
    public function find($id, array $with = [], array $columns = ['*'])
    {
        if ($this->cacheEnabled) {
            $cacheKey = $this->cacheService->generateKey(
                $this->model->getTable() . '_find',
                $id,
                $with,
                $columns
            );
            
            return $this->cacheService->remember($cacheKey, function () use ($id, $with, $columns) {
                return $this->model->with($with)->select($columns)->find($id);
            }, $this->cacheDuration);
        }
        
        return $this->model->with($with)->select($columns)->find($id);
    }

    /**
     * Find record by field
     */
    public function findBy($field, $value, array $with = [], array $columns = ['*'])
    {
        if ($this->cacheEnabled) {
            $cacheKey = $this->cacheService->generateKey(
                $this->model->getTable() . '_findby',
                $field,
                $value,
                $with,
                $columns
            );
            
            return $this->cacheService->remember($cacheKey, function () use ($field, $value, $with, $columns) {
                return $this->model->with($with)->select($columns)->where($field, $value)->first();
            }, $this->cacheDuration);
        }
        
        return $this->model->with($with)->select($columns)->where($field, $value)->first();
    }

    /**
     * Create new record
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->create($data);
            DB::commit();
            
            // Clear cache
            $this->clearCache();
            
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update record
     */
    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->update($data);
            DB::commit();
            
            // Clear cache
            $this->clearCache();
            $this->clearRecordCache($id);
            
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $deleted = $record->delete();
            DB::commit();
            
            // Clear cache
            $this->clearCache();
            $this->clearRecordCache($id);
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk insert with chunking
     */
    public function bulkInsert(array $data, int $chunkSize = 1000)
    {
        DB::beginTransaction();
        try {
            $chunks = array_chunk($data, $chunkSize);
            
            foreach ($chunks as $chunk) {
                $this->model->insert($chunk);
            }
            
            DB::commit();
            
            // Clear cache
            $this->clearCache();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Count records with filters
     */
    public function count(array $filters = [])
    {
        $query = $this->model->newQuery();
        $query = $this->applyFilters($query, $filters);
        
        if ($this->cacheEnabled) {
            $cacheKey = $this->cacheService->generateKey(
                $this->model->getTable() . '_count',
                $filters
            );
            
            return $this->cacheService->remember($cacheKey, function () use ($query) {
                return $query->count();
            }, $this->cacheDuration);
        }
        
        return $query->count();
    }

    /**
     * Check if record exists
     */
    public function exists($id): bool
    {
        if ($this->cacheEnabled) {
            $cacheKey = $this->cacheService->generateKey(
                $this->model->getTable() . '_exists',
                $id
            );
            
            return $this->cacheService->remember($cacheKey, function () use ($id) {
                return $this->model->where('id', $id)->exists();
            }, $this->cacheDuration);
        }
        
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get records with specific columns only
     */
    public function pluck($column, $key = null)
    {
        if ($this->cacheEnabled) {
            $cacheKey = $this->cacheService->generateKey(
                $this->model->getTable() . '_pluck',
                $column,
                $key
            );
            
            return $this->cacheService->remember($cacheKey, function () use ($column, $key) {
                return $this->model->pluck($column, $key);
            }, $this->cacheDuration);
        }
        
        return $this->model->pluck($column, $key);
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }
        
        return $query;
    }

    /**
     * Clear all cache for this model
     */
    public function clearCache()
    {
        $this->cacheService->forgetByPattern($this->model->getTable());
    }

    /**
     * Clear cache for specific record
     */
    public function clearRecordCache($id)
    {
        $patterns = [
            $this->model->getTable() . '_find_' . $id,
            $this->model->getTable() . '_exists_' . $id,
        ];
        
        foreach ($patterns as $pattern) {
            $this->cacheService->forgetByPattern($pattern);
        }
    }

    /**
     * Disable cache temporarily
     */
    public function withoutCache()
    {
        $this->cacheEnabled = false;
        return $this;
    }

    /**
     * Enable cache
     */
    public function withCache()
    {
        $this->cacheEnabled = true;
        return $this;
    }

    /**
     * Execute query with index hint
     */
    public function withIndex(string $index)
    {
        return $this->model->from(DB::raw($this->model->getTable() . ' USE INDEX(' . $index . ')'));
    }

    /**
     * Optimize query with chunking for large datasets
     */
    public function chunk(int $count, callable $callback)
    {
        return $this->model->chunk($count, $callback);
    }

    /**
     * Get query statistics
     */
    public function getQueryStats(): array
    {
        return [
            'total_records' => $this->model->count(),
            'table_size' => DB::select("
                SELECT 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                FROM information_schema.TABLES 
                WHERE table_schema = ? 
                AND table_name = ?
            ", [config('database.connections.mysql.database'), $this->model->getTable()])[0]->size_mb ?? 0,
            'indexes' => DB::select("SHOW INDEXES FROM " . $this->model->getTable()),
        ];
    }
}