<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Services\CacheService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register CacheService as singleton
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        // Register repository bindings
        $this->registerRepositories();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent lazy loading in production
        if ($this->app->environment('production')) {
            Model::preventLazyLoading();
        }

        // Log slow queries in development
        if ($this->app->environment('local', 'development')) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // Log queries taking more than 100ms
                    Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                    ]);
                }
            });
        }

        // Enable query log for debugging (disable in production)
        if (config('app.debug') && !$this->app->environment('production')) {
            DB::enableQueryLog();
        }

        // Set default string length for older MySQL versions
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        // Model observers
        $this->registerObservers();

        // Custom validation rules
        $this->registerValidationRules();
    }

    /**
     * Register repository bindings
     */
    protected function registerRepositories(): void
    {
        // Example repository bindings
        // $this->app->bind(
        //     \App\Repositories\Contracts\ProductRepositoryInterface::class,
        //     \App\Repositories\ProductRepository::class
        // );
    }

    /**
     * Register model observers
     */
    protected function registerObservers(): void
    {
        // Clear cache when models are updated
        \App\Models\Product::saved(function ($product) {
            app(CacheService::class)->clearProductCache();
        });

        \App\Models\Product::deleted(function ($product) {
            app(CacheService::class)->clearProductCache();
        });

        \App\Models\Customer::saved(function ($customer) {
            app(CacheService::class)->clearCustomerCache($customer->id);
        });

        \App\Models\Customer::deleted(function ($customer) {
            app(CacheService::class)->clearCustomerCache($customer->id);
        });

        // Clear dashboard cache when important data changes
        $dashboardModels = [
            \App\Models\Order::class,
            \App\Models\Invoice::class,
            \App\Models\Payment::class,
            \App\Models\ProductionOrder::class,
        ];

        foreach ($dashboardModels as $model) {
            $model::saved(function () {
                app(CacheService::class)->clearDashboardCache();
            });

            $model::deleted(function () {
                app(CacheService::class)->clearDashboardCache();
            });
        }
    }

    /**
     * Register custom validation rules
     */
    protected function registerValidationRules(): void
    {
        // Custom validation rule for phone numbers
        \Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9\-\+\(\)\s]+$/', $value);
        });

        // Custom validation rule for SKU format
        \Validator::extend('sku', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[A-Z0-9\-]+$/', $value);
        });

        // Custom validation rule for tax ID
        \Validator::extend('tax_id', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\.[0-9]{1}\-[0-9]{3}\.[0-9]{3}$/', $value);
        });

        // Custom messages for validation rules
        \Validator::replacer('phone', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, ':attribute must be a valid phone number');
        });

        \Validator::replacer('sku', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, ':attribute must be in uppercase letters, numbers, and hyphens only');
        });

        \Validator::replacer('tax_id', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, ':attribute must be a valid Indonesian tax ID (NPWP)');
        });
    }
}
