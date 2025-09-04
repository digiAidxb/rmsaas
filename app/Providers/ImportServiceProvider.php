<?php

namespace App\Providers;

use App\Services\Import\ImportServiceManager;
use App\Services\Import\Contracts\ImportServiceInterface;
use App\Services\Import\Contracts\FileParserInterface;
use App\Services\Import\Contracts\FieldMapperInterface;
use App\Services\Import\Contracts\ValidationEngineInterface;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the main import service manager as singleton
        $this->app->singleton(ImportServiceManager::class, function ($app) {
            return new ImportServiceManager();
        });

        // Bind interface implementations - these will be implemented in next steps
        $this->app->bind(FileParserInterface::class, function ($app) {
            // Will return appropriate parser based on context
            return $app->make('App\Services\Import\Parsers\CsvParser');
        });

        $this->app->bind(FieldMapperInterface::class, function ($app) {
            return $app->make('App\Services\Import\Mappers\SmartFieldMapper');
        });

        $this->app->bind(ValidationEngineInterface::class, function ($app) {
            return $app->make('App\Services\Import\Validators\ImportValidationEngine');
        });

        // Register import service factories for different types
        $this->registerImportServiceFactories();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Any additional bootstrapping
    }

    /**
     * Register factory methods for different import service types
     */
    protected function registerImportServiceFactories(): void
    {
        // Menu Import Service
        $this->app->bind('import.menu', function ($app) {
            return new \App\Services\Import\Services\MenuImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\MenuFieldMapper'),
                $app->make('App\Services\Import\Validators\ImportValidationEngine'),
                'menu'
            );
        });

        // Inventory Import Service
        $this->app->bind('import.inventory', function ($app) {
            return new \App\Services\Import\InventoryImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\InventoryFieldMapper'),
                $app->make('App\Services\Import\Validators\InventoryValidationEngine'),
                'inventory'
            );
        });

        // Recipe Import Service
        $this->app->bind('import.recipes', function ($app) {
            return new \App\Services\Import\RecipeImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\RecipeFieldMapper'),
                $app->make('App\Services\Import\Validators\RecipeValidationEngine'),
                'recipes'
            );
        });

        // Sales Import Service
        $this->app->bind('import.sales', function ($app) {
            return new \App\Services\Import\SalesImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\SalesFieldMapper'),
                $app->make('App\Services\Import\Validators\SalesValidationEngine'),
                'sales'
            );
        });

        // Customer Import Service
        $this->app->bind('import.customers', function ($app) {
            return new \App\Services\Import\CustomerImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\CustomerFieldMapper'),
                $app->make('App\Services\Import\Validators\CustomerValidationEngine'),
                'customers'
            );
        });

        // Employee Import Service
        $this->app->bind('import.employees', function ($app) {
            return new \App\Services\Import\EmployeeImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\EmployeeFieldMapper'),
                $app->make('App\Services\Import\Validators\EmployeeValidationEngine'),
                'employees'
            );
        });
    }
}