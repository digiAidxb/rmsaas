<?php

namespace App\Console\Commands;

use App\Services\FileStorageService;
use Illuminate\Console\Command;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old temporary import/export files';

    /**
     * Execute the console command.
     */
    public function handle(FileStorageService $fileService)
    {
        $this->info('Starting cleanup of temporary files...');
        
        try {
            $fileService->cleanupOldFiles();
            $this->info('✅ Temporary file cleanup completed successfully.');
        } catch (\Exception $e) {
            $this->error('❌ Failed to cleanup temporary files: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
