<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\AI\Services\ToolRegistry;
use App\AI\Tools\ProductTool;
use App\AI\Tools\StockTool;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(
            ToolRegistry::class,
            function () {
                $registry = new ToolRegistry();

                $registry->register(new ProductTool());
                $registry->register(new StockTool());

                return $registry;
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
