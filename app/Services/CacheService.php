<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Default cache duration in minutes
     */
    protected $defaultDuration = 60; // 1 hour

    /**
     * Cache key prefix
     */
    protected $prefix = 'mdagang_';

    /**
     * Get or set cache data
     */
    public function remember(string $key, $callback, int $minutes = null)
    {
        $minutes = $minutes ?? $this->defaultDuration;
        $cacheKey = $this->prefix . $key;

        try {
            return Cache::remember($cacheKey, now()->addMinutes($minutes), $callback);
        } catch (\Exception $e) {
            Log::warning('Cache remember failed', [
                'key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            // If cache fails, return the callback result directly
            return $callback();
        }
    }

    /**
     * Get cache data
     */
    public function get(string $key, $default = null)
    {
        $cacheKey = $this->prefix . $key;
        
        try {
            return Cache::get($cacheKey, $default);
        } catch (\Exception $e) {
            Log::warning('Cache get failed', [
                'key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Set cache data
     */
    public function put(string $key, $value, int $minutes = null)
    {
        $minutes = $minutes ?? $this->defaultDuration;
        $cacheKey = $this->prefix . $key;

        try {
            return Cache::put($cacheKey, $value, now()->addMinutes($minutes));
        } catch (\Exception $e) {
            Log::warning('Cache put failed', [
                'key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Delete cache data
     */
    public function forget(string $key)
    {
        $cacheKey = $this->prefix . $key;

        try {
            return Cache::forget($cacheKey);
        } catch (\Exception $e) {
            Log::warning('Cache forget failed', [
                'key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clear cache by pattern
     */
    public function forgetByPattern(string $pattern)
    {
        try {
            $keys = Cache::getRedis()->keys($this->prefix . $pattern . '*');
            foreach ($keys as $key) {
                Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
            }
            return true;
        } catch (\Exception $e) {
            Log::warning('Cache pattern forget failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clear all cache
     */
    public function flush()
    {
        try {
            return Cache::flush();
        } catch (\Exception $e) {
            Log::warning('Cache flush failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Cache tags (if supported by driver)
     */
    public function tags(array $tags)
    {
        try {
            return Cache::tags($tags);
        } catch (\Exception $e) {
            // Tags not supported, return regular cache
            return Cache::store();
        }
    }

    /**
     * Generate cache key from parameters
     */
    public function generateKey(string $prefix, ...$params): string
    {
        $key = $prefix;
        foreach ($params as $param) {
            if (is_array($param)) {
                $key .= '_' . md5(json_encode($param));
            } elseif (is_object($param)) {
                $key .= '_' . md5(serialize($param));
            } else {
                $key .= '_' . $param;
            }
        }
        return $key;
    }

    /**
     * Cache dashboard stats
     */
    public function cacheDashboardStats($stats)
    {
        return $this->put('dashboard_stats', $stats, 5); // Cache for 5 minutes
    }

    /**
     * Get cached dashboard stats
     */
    public function getDashboardStats()
    {
        return $this->get('dashboard_stats');
    }

    /**
     * Clear dashboard cache
     */
    public function clearDashboardCache()
    {
        return $this->forget('dashboard_stats');
    }

    /**
     * Cache product list
     */
    public function cacheProducts($page, $perPage, $filters, $products)
    {
        $key = $this->generateKey('products', $page, $perPage, $filters);
        return $this->put($key, $products, 30); // Cache for 30 minutes
    }

    /**
     * Get cached products
     */
    public function getCachedProducts($page, $perPage, $filters)
    {
        $key = $this->generateKey('products', $page, $perPage, $filters);
        return $this->get($key);
    }

    /**
     * Clear product cache
     */
    public function clearProductCache()
    {
        return $this->forgetByPattern('products');
    }

    /**
     * Cache customer data
     */
    public function cacheCustomer($customerId, $customer)
    {
        return $this->put("customer_{$customerId}", $customer, 60);
    }

    /**
     * Get cached customer
     */
    public function getCachedCustomer($customerId)
    {
        return $this->get("customer_{$customerId}");
    }

    /**
     * Clear customer cache
     */
    public function clearCustomerCache($customerId = null)
    {
        if ($customerId) {
            return $this->forget("customer_{$customerId}");
        }
        return $this->forgetByPattern('customer_');
    }
}