<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Models\Tenant;

class FileStorageService
{
    /**
     * Store a tenant-specific file
     */
    public function storeTenantFile(UploadedFile $file, string $directory = '', string $disk = 'tenant_local'): string
    {
        $tenant = Tenant::current();
        if (!$tenant) {
            throw new \Exception('No current tenant found');
        }

        $tenantDirectory = "tenant_{$tenant->id}";
        $fullDirectory = $directory ? "{$tenantDirectory}/{$directory}" : $tenantDirectory;
        
        $filename = $this->generateUniqueFilename($file);
        
        $path = $file->storeAs($fullDirectory, $filename, $disk);
        
        return $path;
    }

    /**
     * Store an import file
     */
    public function storeImportFile(UploadedFile $file): string
    {
        $filename = $this->generateUniqueFilename($file);
        $path = $file->storeAs('', $filename, 'imports');
        
        return $path;
    }

    /**
     * Store an export file
     */
    public function storeExportFile(string $content, string $originalName, string $extension = 'csv'): string
    {
        $filename = $this->generateFilename($originalName, $extension);
        
        Storage::disk('exports')->put($filename, $content);
        
        return $filename;
    }

    /**
     * Get a tenant file URL
     */
    public function getTenantFileUrl(string $path, string $disk = 'tenant_public'): string
    {
        return Storage::disk($disk)->url($path);
    }

    /**
     * Delete a tenant file
     */
    public function deleteTenantFile(string $path, string $disk = 'tenant_local'): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $path, string $disk = 'tenant_local'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get file contents
     */
    public function getFileContents(string $path, string $disk = 'tenant_local'): string
    {
        return Storage::disk($disk)->get($path);
    }

    /**
     * Move file from import to tenant storage
     */
    public function moveImportToTenant(string $importPath, string $tenantDirectory = 'uploads'): string
    {
        $content = Storage::disk('imports')->get($importPath);
        $filename = basename($importPath);
        
        $tenant = Tenant::current();
        if (!$tenant) {
            throw new \Exception('No current tenant found');
        }

        $tenantPath = "tenant_{$tenant->id}/{$tenantDirectory}/{$filename}";
        Storage::disk('tenant_local')->put($tenantPath, $content);
        
        // Clean up import file
        Storage::disk('imports')->delete($importPath);
        
        return $tenantPath;
    }

    /**
     * Clean up old temporary files
     */
    public function cleanupOldFiles(): void
    {
        $cutoff = now()->subHours(24);
        
        // Clean imports older than 24 hours
        $importFiles = Storage::disk('imports')->allFiles();
        foreach ($importFiles as $file) {
            if (Storage::disk('imports')->lastModified($file) < $cutoff->timestamp) {
                Storage::disk('imports')->delete($file);
            }
        }
        
        // Clean exports older than 24 hours
        $exportFiles = Storage::disk('exports')->allFiles();
        foreach ($exportFiles as $file) {
            if (Storage::disk('exports')->lastModified($file) < $cutoff->timestamp) {
                Storage::disk('exports')->delete($file);
            }
        }
    }

    /**
     * Generate a unique filename
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $basename = Str::slug($basename);
        
        return $basename . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Generate a filename for exports
     */
    private function generateFilename(string $name, string $extension): string
    {
        $basename = Str::slug(pathinfo($name, PATHINFO_FILENAME));
        return $basename . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
    }

    /**
     * Get storage usage for tenant
     */
    public function getTenantStorageUsage(): array
    {
        $tenant = Tenant::current();
        if (!$tenant) {
            return ['size' => 0, 'files' => 0];
        }

        $tenantDirectory = "tenant_{$tenant->id}";
        $files = Storage::disk('tenant_local')->allFiles($tenantDirectory);
        
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += Storage::disk('tenant_local')->size($file);
        }

        return [
            'size' => $totalSize,
            'files' => count($files),
            'size_formatted' => $this->formatBytes($totalSize),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}