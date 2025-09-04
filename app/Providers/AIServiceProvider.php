<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AI\LossManagementService;
use App\Services\AI\ProfitOptimizationService;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->app->singleton(LossManagementService::class, function ($app) {
            return new LossManagementService();
        });

        $this->app->singleton(ProfitOptimizationService::class, function ($app) {
            return new ProfitOptimizationService();
        });
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        //
    }
}