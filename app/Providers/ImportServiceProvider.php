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

        // Bind interface implementations - dynamic parser selection
        $this->app->bind(FileParserInterface::class, function ($app) {
            // Default to CsvParser - will be overridden in factory methods
            return $app->make('App\Services\Import\Parsers\CsvParser');
        });

        // Register parser factory for dynamic parser selection
        $this->app->singleton('FileParserFactory', function ($app) {
            return new class {
                public function createParser($mimeType, $extension = null) {
                    $excelMimes = [
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'application/vnd.oasis.opendocument.spreadsheet',
                        'application/excel',
                        'application/vnd.msexcel'
                    ];

                    $excelExtensions = ['xlsx', 'xls', 'ods'];

                    if (in_array($mimeType, $excelMimes) || ($extension && in_array(strtolower($extension), $excelExtensions))) {
                        return app('App\Services\Import\Parsers\ExcelParser');
                    }

                    if ($mimeType === 'application/json' || ($extension && strtolower($extension) === 'json')) {
                        return app('App\Services\Import\Parsers\JsonParser');
                    }

                    // Default to CSV parser
                    return app('App\Services\Import\Parsers\CsvParser');
                }
            };
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
        // Menu Import Service - only register services that actually exist
        $this->app->bind('import.menu', function ($app) {
            return new \App\Services\Import\Services\MenuImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\MenuFieldMapper'),
                $app->make('App\Services\Import\Validators\ImportValidationEngine'),
                'menu'
            );
        });

        // For other import types, use the generic MenuImportService as fallback
        // until specific services are implemented
        $this->app->bind('import.inventory', function ($app) {
            return new \App\Services\Import\Services\MenuImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\SmartFieldMapper'),
                $app->make('App\Services\Import\Validators\ImportValidationEngine'),
                'inventory'
            );
        });

        $this->app->bind('import.recipes', function ($app) {
            return new \App\Services\Import\Services\MenuImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\SmartFieldMapper'),
                $app->make('App\Services\Import\Validators\ImportValidationEngine'),
                'recipes'
            );
        });

        $this->app->bind('import.sales', function ($app) {
            return new \App\Services\Import\Services\MenuImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\SmartFieldMapper'),
                $app->make('App\Services\Import\Validators\ImportValidationEngine'),
                'sales'
            );
        });

        $this->app->bind('import.customers', function ($app) {
            return new \App\Services\Import\Services\MenuImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\SmartFieldMapper'),
                $app->make('App\Services\Import\Validators\ImportValidationEngine'),
                'customers'
            );
        });

        $this->app->bind('import.employees', function ($app) {
            return new \App\Services\Import\Services\MenuImportService(
                $app->make('App\Services\Import\Parsers\CsvParser'),
                $app->make('App\Services\Import\Mappers\SmartFieldMapper'),
                $app->make('App\Services\Import\Validators\ImportValidationEngine'),
                'employees'
            );
        });
    }
}