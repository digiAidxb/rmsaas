<?php

if (!function_exists('formatBytes')) {
    /**
     * Format bytes into human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Alias for formatBytes for consistency
     */
    function formatFileSize($bytes, $precision = 2)
    {
        return formatBytes($bytes, $precision);
    }
}